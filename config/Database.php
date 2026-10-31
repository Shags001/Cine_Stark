<?php

class Database
{
    private static $instance = null;
    private $connection;

    private $host = 'localhost';
    private $dbName = 'cine_stark';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';

    private function __construct()
    {
        $ports = [3306, 3307];
        $lastError = null;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        foreach ($ports as $port) {
            try {
                $dsn = "mysql:host={$this->host};port={$port};dbname={$this->dbName};charset={$this->charset}";
                $this->connection = new PDO($dsn, $this->username, $this->password, $options);
                $this->connection->exec("SET time_zone = '-06:00'");
                return;
            } catch (PDOException $e) {
                $lastError = $e;
            }
        }

        throw new PDOException("No se pudo conectar en los puertos 3306 ni 3307: " . $lastError->getMessage());
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }

    private function __clone() {}
}