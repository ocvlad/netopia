<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\UserService;
use App\Services\AuthService;
use App\Services\LoggerService;

class AuthController
{
    private $userService;
    private $authService;
    private $logger;

    public function __construct(UserService $userService, AuthService $authService, LoggerService $logger)
    {
        $this->userService = $userService;
        $this->authService = $authService;
        $this->logger = $logger;
    }

    public function login(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $user = $this->userService->authenticate($username, $password);

        if ($user) {
            $payload = [
                'iss' => "http://localhost",  // Issuer
                'iat' => time(),
                'exp' => time() + 3600,
                'sub' => $user['id'],
            ];
            $jwt = $this->authService->generateToken($payload);

            $response = new Response();
            $response->setContent(json_encode(['token' => $jwt], JSON_UNESCAPED_SLASHES));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        $this->logger->warning("Failed login attempt: $username");

        return new Response('Unauthorized', 401);
    }
}
