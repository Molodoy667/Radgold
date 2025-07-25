<?php
namespace App\Models;

class Setting {
    public $id;
    public $setting_key;
    public $setting_value;
    public $setting_type;
    public $description;
    public $is_public;
    public $updated_at;

    public static function get($key, $db, $default = null) {
        $stmt = $db->prepare("SELECT setting_value, setting_type FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$result) {
            return $default;
        }

        switch ($result['setting_type']) {
            case 'boolean':
                return (bool)$result['setting_value'];
            case 'number':
                return is_numeric($result['setting_value']) ? (float)$result['setting_value'] : $default;
            case 'json':
                return json_decode($result['setting_value'], true);
            default:
                return $result['setting_value'];
        }
    }

    public static function set($key, $value, $db) {
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) 
                             VALUES (?, ?, ?) 
                             ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
        
        $type = self::getValueType($value);
        $stringValue = self::valueToString($value);
        
        return $stmt->execute([$key, $stringValue, $type, $stringValue]);
    }

    public static function getAll($db, $publicOnly = false) {
        $sql = "SELECT setting_key, setting_value, setting_type, description, is_public 
                FROM settings";
        if ($publicOnly) {
            $sql .= " WHERE is_public = TRUE";
        }
        $sql .= " ORDER BY setting_key";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = self::parseValue($row['setting_value'], $row['setting_type']);
        }
        
        return $settings;
    }

    public static function delete($key, $db) {
        $stmt = $db->prepare("DELETE FROM settings WHERE setting_key = ?");
        return $stmt->execute([$key]);
    }

    public static function exists($key, $db) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    private static function getValueType($value) {
        if (is_bool($value)) {
            return 'boolean';
        } elseif (is_numeric($value)) {
            return 'number';
        } elseif (is_array($value) || is_object($value)) {
            return 'json';
        } else {
            return 'string';
        }
    }

    private static function valueToString($value) {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        } elseif (is_array($value) || is_object($value)) {
            return json_encode($value);
        } else {
            return (string)$value;
        }
    }

    private static function parseValue($value, $type) {
        switch ($type) {
            case 'boolean':
                return (bool)$value;
            case 'number':
                return is_numeric($value) ? (float)$value : $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    public static function getPublicSettings($db) {
        return self::getAll($db, true);
    }

    public static function updateMultiple($settings, $db) {
        $db->beginTransaction();
        try {
            foreach ($settings as $key => $value) {
                self::set($key, $value, $db);
            }
            $db->commit();
            return true;
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}