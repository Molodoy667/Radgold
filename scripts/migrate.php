<?php
/**
 * Скрипт миграции базы данных
 * Использование: php scripts/migrate.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Загружаем переменные окружения
\App\Core\Environment::load();

class DatabaseMigrator {
    private $db;
    private $migrationsPath;
    
    public function __construct() {
        $this->db = \App\Core\Router::getDb();
        $this->migrationsPath = __DIR__ . '/../database/migrations/';
        
        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }
    }
    
    public function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->exec($sql);
        echo "✅ Таблица миграций создана\n";
    }
    
    public function getRanMigrations() {
        $sql = "SELECT migration FROM migrations ORDER BY id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    
    public function getPendingMigrations() {
        $files = glob($this->migrationsPath . '*.php');
        $ranMigrations = $this->getRanMigrations();
        
        $pending = [];
        foreach ($files as $file) {
            $migration = basename($file, '.php');
            if (!in_array($migration, $ranMigrations)) {
                $pending[] = $migration;
            }
        }
        
        return $pending;
    }
    
    public function runMigrations() {
        $this->createMigrationsTable();
        
        $pending = $this->getPendingMigrations();
        
        if (empty($pending)) {
            echo "✅ Все миграции уже выполнены\n";
            return;
        }
        
        $batch = $this->getNextBatchNumber();
        
        foreach ($pending as $migration) {
            echo "🔄 Выполняется миграция: {$migration}\n";
            
            try {
                $this->runMigration($migration, $batch);
                echo "✅ Миграция {$migration} выполнена успешно\n";
            } catch (Exception $e) {
                echo "❌ Ошибка в миграции {$migration}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
        
        echo "🎉 Все миграции выполнены успешно!\n";
    }
    
    private function runMigration($migration, $batch) {
        $file = $this->migrationsPath . $migration . '.php';
        
        if (!file_exists($file)) {
            throw new Exception("Файл миграции не найден: {$file}");
        }
        
        require_once $file;
        
        $className = $this->getMigrationClassName($migration);
        
        if (!class_exists($className)) {
            throw new Exception("Класс миграции не найден: {$className}");
        }
        
        $instance = new $className();
        
        if (method_exists($instance, 'up')) {
            $instance->up($this->db);
        } else {
            throw new Exception("Метод 'up' не найден в классе {$className}");
        }
        
        // Записываем выполненную миграцию
        $sql = "INSERT INTO migrations (migration, batch) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$migration, $batch]);
    }
    
    private function getMigrationClassName($migration) {
        $parts = explode('_', $migration, 4);
        if (count($parts) < 4) {
            throw new Exception("Неверный формат имени миграции: {$migration}");
        }
        
        $className = '';
        for ($i = 3; $i < count($parts); $i++) {
            $className .= ucfirst($parts[$i]);
        }
        
        return $className;
    }
    
    private function getNextBatchNumber() {
        $sql = "SELECT MAX(batch) as max_batch FROM migrations";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return ($result['max_batch'] ?? 0) + 1;
    }
    
    public function createMigration($name) {
        $timestamp = date('Y_m_d_His');
        $filename = $timestamp . '_' . $name . '.php';
        $filepath = $this->migrationsPath . $filename;
        
        $className = $this->getClassNameFromName($name);
        
        $content = "<?php

use PDO;

class {$className} {
    public function up(\$db) {
        // Здесь код для создания/изменения таблиц
        \$sql = \"\";
        \$db->exec(\$sql);
    }
    
    public function down(\$db) {
        // Здесь код для отката изменений
        \$sql = \"\";
        \$db->exec(\$sql);
    }
}
";
        
        file_put_contents($filepath, $content);
        echo "✅ Создана миграция: {$filename}\n";
    }
    
    private function getClassNameFromName($name) {
        $parts = explode('_', $name);
        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        return $className;
    }
    
    public function rollback($steps = 1) {
        $sql = "SELECT migration FROM migrations ORDER BY id DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$steps]);
        $migrations = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        foreach ($migrations as $migration) {
            echo "🔄 Откат миграции: {$migration}\n";
            
            try {
                $this->rollbackMigration($migration);
                echo "✅ Миграция {$migration} откачена успешно\n";
            } catch (Exception $e) {
                echo "❌ Ошибка при откате миграции {$migration}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
    }
    
    private function rollbackMigration($migration) {
        $file = $this->migrationsPath . $migration . '.php';
        
        if (!file_exists($file)) {
            throw new Exception("Файл миграции не найден: {$file}");
        }
        
        require_once $file;
        
        $className = $this->getMigrationClassName($migration);
        
        if (!class_exists($className)) {
            throw new Exception("Класс миграции не найден: {$className}");
        }
        
        $instance = new $className();
        
        if (method_exists($instance, 'down')) {
            $instance->down($this->db);
        } else {
            throw new Exception("Метод 'down' не найден в классе {$className}");
        }
        
        // Удаляем запись о миграции
        $sql = "DELETE FROM migrations WHERE migration = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$migration]);
    }
    
    public function status() {
        $this->createMigrationsTable();
        
        $ranMigrations = $this->getRanMigrations();
        $pendingMigrations = $this->getPendingMigrations();
        
        echo "📊 Статус миграций:\n\n";
        
        if (!empty($ranMigrations)) {
            echo "✅ Выполненные миграции:\n";
            foreach ($ranMigrations as $migration) {
                echo "  - {$migration}\n";
            }
            echo "\n";
        }
        
        if (!empty($pendingMigrations)) {
            echo "⏳ Ожидающие миграции:\n";
            foreach ($pendingMigrations as $migration) {
                echo "  - {$migration}\n";
            }
            echo "\n";
        }
        
        if (empty($ranMigrations) && empty($pendingMigrations)) {
            echo "📁 Миграции не найдены\n";
        }
    }
}

// Обработка аргументов командной строки
$command = $argv[1] ?? 'run';

$migrator = new DatabaseMigrator();

switch ($command) {
    case 'run':
        $migrator->runMigrations();
        break;
    case 'create':
        $name = $argv[2] ?? null;
        if (!$name) {
            echo "❌ Укажите имя миграции: php scripts/migrate.php create migration_name\n";
            exit(1);
        }
        $migrator->createMigration($name);
        break;
    case 'rollback':
        $steps = (int)($argv[2] ?? 1);
        $migrator->rollback($steps);
        break;
    case 'status':
        $migrator->status();
        break;
    default:
        echo "📖 Использование:\n";
        echo "  php scripts/migrate.php run          - выполнить все миграции\n";
        echo "  php scripts/migrate.php create name  - создать новую миграцию\n";
        echo "  php scripts/migrate.php rollback [n] - откатить последние n миграций\n";
        echo "  php scripts/migrate.php status       - показать статус миграций\n";
        break;
}