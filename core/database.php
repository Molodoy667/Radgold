<?php
// Клас для сумісності з старими версіями MySQL extension
class DatabaseResultCompat {
    private $data;
    private $position = 0;
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function fetch_assoc() {
        if ($this->position < count($this->data)) {
            return $this->data[$this->position++];
        }
        return null;
    }
    
    public function fetch_array($type = MYSQLI_BOTH) {
        if ($this->position < count($this->data)) {
            $row = $this->data[$this->position++];
            if ($type === MYSQLI_NUM) {
                return array_values($row);
            } elseif ($type === MYSQLI_ASSOC) {
                return $row;
            } else {
                return array_merge(array_values($row), $row);
            }
        }
        return null;
    }
    
    public function fetch_row() {
        return $this->fetch_array(MYSQLI_NUM);
    }
    
    public function free() {
        $this->data = [];
    }
    
    public function num_rows() {
        return count($this->data);
    }
    
    public $num_rows;
    
    public function __get($name) {
        if ($name === 'num_rows') {
            return count($this->data);
        }
        return null;
    }
}

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            $this->connection->set_charset("utf8mb4");
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                die("Database connection error: " . $e->getMessage());
            } else {
                die("Database connection error occurred.");
            }
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        
        // Перевіряємо чи доступний get_result() (потрібен mysqlnd)
        if (method_exists($stmt, 'get_result')) {
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            // Fallback для старих версій MySQL extension
            $meta = $stmt->result_metadata();
            $fields = [];
            $row = [];
            
            if ($meta) {
                while ($field = $meta->fetch_field()) {
                    $fields[] = &$row[$field->name];
                }
                call_user_func_array([$stmt, 'bind_result'], $fields);
                
                $results = [];
                while ($stmt->fetch()) {
                    $results[] = array_map(function($x) { return $x; }, $row);
                }
                $stmt->close();
                
                // Створюємо псевдо-результат який працює як mysqli_result
                return new DatabaseResultCompat($results);
            } else {
                $stmt->close();
                return true; // для INSERT/UPDATE/DELETE запитів
            }
        }
    }
    
    public function insert($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Execute failed: " . $error);
        }
        
        $insertId = $this->connection->insert_id;
        $stmt->close();
        
        return $insertId;
    }
    
    public function update($sql, $params = []) {
        return $this->execute($sql, $params);
    }
    
    public function delete($sql, $params = []) {
        return $this->execute($sql, $params);
    }
    
    private function execute($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }
        
        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Execute failed: " . $error);
        }
        
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        return $affectedRows;
    }
    
    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }
    
    public function directQuery($sql) {
        $result = $this->connection->query($sql);
        if (!$result) {
            throw new Exception("Query failed: " . $this->connection->error);
        }
        return $result;
    }
    
    public function close() {
        $this->connection->close();
    }
}
?>
