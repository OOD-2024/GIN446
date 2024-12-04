<?php

declare(strict_types=1);
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
      $port = 3306;
      $host = $_ENV['DB_HOST'] ?? 'db';
      $dbusername = $_ENV['DB_USER'] ?? 'user';
      $dbpassword = $_ENV['DB_PASS'] ?? 'pass';
      $dbname = $_ENV['DB_NAME'] ?? 'clinic';     
        try {
            $this->pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $dbusername, $dbpassword);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
