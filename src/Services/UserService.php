<?php

namespace App\Services;

class UserService
{
    private $db;

    public function __construct(DatabaseService $databaseService)
    {
        $this->db = $databaseService->getConnection();
    }

    public function authenticate($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch();

        if ($result && password_verify($password, $result['password_hash'])) {
            return $result;
        }

        return false;
    }
}
