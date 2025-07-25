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

    public function register($db) {
        $email = $_POST['email'] ?? '';
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Проверка существования пользователя
        $existingUser = User::findByLogin($login, $db);
        if ($existingUser) {
            echo json_encode(['success' => false, 'error' => 'Пользователь с таким логином уже существует']);
            return;
        }
        
        // Создание нового пользователя
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userData = [
            'email' => $email,
            'login' => $login,
            'password' => $hashedPassword,
            'role' => 'user'
        ];
        
        if (User::create($userData, $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при создании пользователя']);
        }
    }
}