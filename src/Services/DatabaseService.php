<?php

namespace App\Services;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class DatabaseService
{
    private $connection;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        try {
            $dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];

            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new \Exception("Database connection error: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
