<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private ?PDO $connection = null;
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/database.php';
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }

        return $this->connection;
    }

    private function connect(): void
    {
        $config = $this->config['connections'][$this->config['default']];
        
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            throw new \Exception('Ошибка подключения к базе данных: ' . $e->getMessage());
        }
    }

    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    public function rollback(): bool
    {
        return $this->getConnection()->rollback();
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function lastInsertId(): string
    {
        return $this->getConnection()->lastInsertId();
    }
}