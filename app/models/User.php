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
}