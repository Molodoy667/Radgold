<?php
namespace App\Models;

class User {
    public $id;
    public $email;
    public $login;
    public $password;
    public $avatar;
    public $status;
    public $role;
    public $created_at;

    public static function findByLogin($login, $db) {
        $stmt = $db->prepare("SELECT * FROM users WHERE login = ? LIMIT 1");
        $stmt->execute([$login]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function create($data, $db) {
        $stmt = $db->prepare("INSERT INTO users (email, login, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$data['email'], $data['login'], $data['password'], $data['role'] ?? 'user']);
    }
}