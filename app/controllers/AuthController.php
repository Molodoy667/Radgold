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

        // Валидация
        if (empty($email) || empty($login) || empty($password)) {
            echo json_encode(['success' => false, 'error' => 'Заполните все поля']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Неверный формат email']);
            return;
        }

        if (strlen($login) < 3) {
            echo json_encode(['success' => false, 'error' => 'Логин должен содержать минимум 3 символа']);
            return;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'error' => 'Пароль должен содержать минимум 6 символов']);
            return;
        }

        // Проверка существования пользователя
        $existingUser = User::findByLogin($login, $db);
        if ($existingUser) {
            echo json_encode(['success' => false, 'error' => 'Пользователь с таким логином уже существует']);
            return;
        }

        // Проверка email
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            echo json_encode(['success' => false, 'error' => 'Пользователь с таким email уже существует']);
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
