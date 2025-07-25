<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    public function login($db) {
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        $user = User::findByLogin($login, $db);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Неверный логин или пароль']);
        }
    }
    public function logout() {
        unset($_SESSION['user']);
        header('Location: /login');
        exit;
    }
}