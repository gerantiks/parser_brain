<?php

namespace app;

class Database
{
    private array $data;

    private string $sql;

    private $pdo;

    public array $options;

    public function setSql($value): void
    {
        $this->sql=$value;
    }
    public function setData($value): void
    {
        $this->data=$value;
    }

    public function __construct($host, $database, $user, $password)
    {
        $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
        try {
            $this->pdo = new \PDO($dsn, $user, $password);
        } catch (\PDOException $e) {
            die("Помилка при підключення до бази: {$e->getMessage()}");
        }
    }

    public function setOptions(): void
    {
        foreach ($this->data as $item) {
            $this->options[] = $item;
        }
    }

    public function query(): void
    {
        try{
            $conn = $this->pdo;
            $stmt = $conn->prepare($this->sql);
            $this->setOptions();
            $stmt->execute($this->options);
            $this->options = [];
        } catch(\Exception $e) {
            echo 'Ошибка: '. $e->getMessage();
        }

    }

    public function fetch(): array
    {
        try{
            $conn = $this->pdo;;
            $result = $conn->query($this->sql)->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            echo 'Ошибка: '. $e->getMessage();
        }
        return $result;
    }

}