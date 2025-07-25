<?php
namespace App\Core;

class Validator {
    private $errors = [];
    private $data = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function required($field, $message = null) {
        $value = $this->getValue($field);
        if (empty($value) && $value !== '0') {
            $this->addError($field, $message ?? "Поле {$field} обязательно для заполнения");
        }
        return $this;
    }
    
    public function email($field, $message = null) {
        $value = $this->getValue($field);
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message ?? "Поле {$field} должно быть корректным email адресом");
        }
        return $this;
    }
    
    public function minLength($field, $min, $message = null) {
        $value = $this->getValue($field);
        if (!empty($value) && mb_strlen($value) < $min) {
            $this->addError($field, $message ?? "Поле {$field} должно содержать минимум {$min} символов");
        }
        return $this;
    }
    
    public function maxLength($field, $max, $message = null) {
        $value = $this->getValue($field);
        if (!empty($value) && mb_strlen($value) > $max) {
            $this->addError($field, $message ?? "Поле {$field} должно содержать максимум {$max} символов");
        }
        return $this;
    }
    
    public function numeric($field, $message = null) {
        $value = $this->getValue($field);
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, $message ?? "Поле {$field} должно быть числом");
        }
        return $this;
    }
    
    public function min($field, $min, $message = null) {
        $value = $this->getValue($field);
        if (!empty($value) && is_numeric($value) && $value < $min) {
            $this->addError($field, $message ?? "Поле {$field} должно быть не меньше {$min}");
        }
        return $this;
    }
    
    public function max($field, $max, $message = null) {
        $value = $this->getValue($field);
        if (!empty($value) && is_numeric($value) && $value > $max) {
            $this->addError($field, $message ?? "Поле {$field} должно быть не больше {$max}");
        }
        return $this;
    }
    
    public function in($field, $allowedValues, $message = null) {
        $value = $this->getValue($field);
        if (!empty($value) && !in_array($value, $allowedValues)) {
            $this->addError($field, $message ?? "Поле {$field} должно быть одним из: " . implode(', ', $allowedValues));
        }
        return $this;
    }
    
    public function regex($field, $pattern, $message = null) {
        $value = $this->getValue($field);
        if (!empty($value) && !preg_match($pattern, $value)) {
            $this->addError($field, $message ?? "Поле {$field} имеет неверный формат");
        }
        return $this;
    }
    
    public function unique($field, $table, $column = null, $excludeId = null, $message = null) {
        $value = $this->getValue($field);
        if (!empty($value)) {
            $column = $column ?: $field;
            $db = \App\Core\Router::getDb();
            
            $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
            $params = [$value];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                $this->addError($field, $message ?? "Поле {$field} уже существует");
            }
        }
        return $this;
    }
    
    public function file($field, $allowedTypes = [], $maxSize = null, $message = null) {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return $this;
        }
        
        $file = $_FILES[$field];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->addError($field, $message ?? "Ошибка загрузки файла");
            return $this;
        }
        
        if (!empty($allowedTypes)) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedTypes)) {
                $this->addError($field, $message ?? "Недопустимый тип файла. Разрешены: " . implode(', ', $allowedTypes));
            }
        }
        
        if ($maxSize && $file['size'] > $maxSize) {
            $this->addError($field, $message ?? "Размер файла превышает допустимый лимит");
        }
        
        return $this;
    }
    
    public function sanitize($field, $type = 'string') {
        $value = $this->getValue($field);
        
        switch ($type) {
            case 'string':
                $value = trim(strip_tags($value));
                break;
            case 'email':
                $value = filter_var($value, FILTER_SANITIZE_EMAIL);
                break;
            case 'url':
                $value = filter_var($value, FILTER_SANITIZE_URL);
                break;
            case 'int':
                $value = (int) $value;
                break;
            case 'float':
                $value = (float) $value;
                break;
            case 'html':
                $value = trim($value);
                break;
        }
        
        $this->data[$field] = $value;
        return $this;
    }
    
    private function getValue($field) {
        return $this->data[$field] ?? '';
    }
    
    private function addError($field, $message) {
        $this->errors[$field][] = $message;
    }
    
    public function fails() {
        return !empty($this->errors);
    }
    
    public function passes() {
        return empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getFirstError($field) {
        return $this->errors[$field][0] ?? null;
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function getValue($field) {
        return $this->data[$field] ?? '';
    }
}