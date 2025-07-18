<?php
// Конфігурація бази даних
class Database {
    private $host = 'localhost';
    private $db_name = 'classifieds_board';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Підключення до бази даних
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Помилка підключення: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>