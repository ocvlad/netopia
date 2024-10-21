<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Services\UserService;


class AuthService
{
    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var \App\Services\UserService
     */
    private $userService;

    /**
     * @param \App\Services\UserService $userService
     * @param string $secretKey
     */
    public function __construct(UserService $userService, string $secretKey)
    {
        $this->userService = $userService;
        $this->secretKey = $secretKey;
    }

    /**
     * @param array $payload
     * @return string
     */
    public function generateToken(array $payload): string
    {
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    /**
     * @param string $token
     * @return array|null
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param string $token
     * @return array|null
     */
    public function getPayloadFromToken(string $token): ?array
    {
        $decoded = $this->validateToken($token);
        return $decoded ? (array) $decoded : null;
    }
}
