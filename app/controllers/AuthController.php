<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->userModel = new User($db);
    }

    public function loginForm(): void
    {
        // Если пользователь уже авторизован, перенаправляем
        if (isset($_SESSION['user'])) {
            $this->redirect('/profile');
        }

        $this->view('auth/login', [
            'title' => 'Вход в аккаунт',
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function login(): void
    {
        $input = $this->getInput();
        
        // Проверяем CSRF токен
        if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Неверный токен безопасности'], 400);
        }

        // Валидация
        $errors = $this->validateLoginData($input);
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
        }

        // Поиск пользователя
        $user = $this->userModel->findByEmail($input['email']) ?: 
                $this->userModel->findByUsername($input['email']);

        if (!$user) {
            $this->json(['success' => false, 'message' => 'Неверный email или пароль'], 401);
        }

        // Проверка пароля
        if (!password_verify($input['password'], $user['password_hash'])) {
            $this->json(['success' => false, 'message' => 'Неверный email или пароль'], 401);
        }

        // Проверка статуса пользователя
        if ($user['status'] === 'banned') {
            $this->json(['success' => false, 'message' => 'Ваш аккаунт заблокирован'], 403);
        }

        // Успешная авторизация
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        $_SESSION['login_time'] = time();

        $this->json([
            'success' => true, 
            'message' => 'Добро пожаловать!',
            'redirect' => '/profile'
        ]);
    }

    public function registerForm(): void
    {
        // Если пользователь уже авторизован, перенаправляем
        if (isset($_SESSION['user'])) {
            $this->redirect('/profile');
        }

        $this->view('auth/register', [
            'title' => 'Регистрация',
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function register(): void
    {
        $input = $this->getInput();
        
        // Проверяем CSRF токен
        if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Неверный токен безопасности'], 400);
        }

        // Валидация
        $errors = $this->validateRegisterData($input);
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
        }

        try {
            // Создание пользователя
            $userId = $this->userModel->create([
                'email' => $input['email'],
                'username' => $input['username'],
                'password' => $input['password']
            ]);

            // Автоматическая авторизация
            $user = $this->userModel->findById($userId);
            $_SESSION['user'] = $user;
            $_SESSION['login_time'] = time();

            $this->json([
                'success' => true, 
                'message' => 'Регистрация прошла успешно!',
                'redirect' => '/profile'
            ]);

        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Ошибка при регистрации'], 500);
        }
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/');
    }

    private function validateLoginData(array $data): array
    {
        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email или логин обязателен';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Пароль обязателен';
        }

        return $errors;
    }

    private function validateRegisterData(array $data): array
    {
        $errors = [];

        // Email
        if (empty($data['email'])) {
            $errors['email'] = 'Email обязателен';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Неверный формат email';
        } elseif ($this->userModel->findByEmail($data['email'])) {
            $errors['email'] = 'Пользователь с таким email уже существует';
        }

        // Username
        if (empty($data['username'])) {
            $errors['username'] = 'Логин обязателен';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Логин должен содержать минимум 3 символа';
        } elseif (strlen($data['username']) > 20) {
            $errors['username'] = 'Логин не должен превышать 20 символов';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors['username'] = 'Логин может содержать только буквы, цифры и символ _';
        } elseif ($this->userModel->findByUsername($data['username'])) {
            $errors['username'] = 'Пользователь с таким логином уже существует';
        }

        // Password
        if (empty($data['password'])) {
            $errors['password'] = 'Пароль обязателен';
        } elseif (strlen($data['password']) < $this->config['security']['password_min_length']) {
            $errors['password'] = 'Пароль должен содержать минимум ' . 
                                $this->config['security']['password_min_length'] . ' символов';
        }

        // Password confirmation
        if (empty($data['password_confirm'])) {
            $errors['password_confirm'] = 'Подтверждение пароля обязательно';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Пароли не совпадают';
        }

        return $errors;
    }
}