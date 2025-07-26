<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Favorite;

class UserController extends Controller
{
    private User $userModel;
    private Product $productModel;
    private Favorite $favoriteModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->userModel = new User($db);
        $this->productModel = new Product($db);
        $this->favoriteModel = new Favorite($db);
    }

    public function profile(): void
    {
        $this->requireAuth();
        
        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $this->redirect('/login');
            return;
        }
        
        // Получаем статистику пользователя
        $stats = [
            'products_count' => $this->getProductsCount($userId),
            'favorites_count' => $this->favoriteModel->getUserFavoritesCount($userId),
            'purchases_count' => $this->getPurchasesCount($userId),
            'reviews_count' => $this->getReviewsCount($userId)
        ];
        
        // Последние товары
        $recentProducts = $this->productModel->findByUser($userId, 1, 5);
        
        // Последние избранные
        $recentFavorites = $this->favoriteModel->getUserFavorites($userId, 1, 5);
        
        $this->view('user/profile', [
            'title' => 'Мой профиль',
            'user' => $user,
            'stats' => $stats,
            'recentProducts' => $recentProducts,
            'recentFavorites' => $recentFavorites
        ]);
    }

    public function editProfile(): void
    {
        $this->requireAuth();
        
        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $this->redirect('/login');
            return;
        }
        
        $this->view('user/edit-profile', [
            'title' => 'Редактирование профиля',
            'user' => $user,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function updateProfile(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $userId = $_SESSION['user']['id'];
        
        // Проверяем CSRF токен
        if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Неверный токен безопасности'], 400);
        }
        
        // Валидация данных
        $errors = $this->validateProfileData($input);
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
        }
        
        try {
            $updateData = [
                'username' => $input['username'],
                'email' => $input['email'],
                'first_name' => $input['first_name'] ?? '',
                'last_name' => $input['last_name'] ?? '',
                'bio' => $input['bio'] ?? '',
                'phone' => $input['phone'] ?? '',
                'telegram' => $input['telegram'] ?? '',
                'discord' => $input['discord'] ?? ''
            ];
            
            // Обработка аватара
            if (!empty($_FILES['avatar']['tmp_name'])) {
                $avatarPath = $this->handleAvatarUpload();
                if ($avatarPath) {
                    $updateData['avatar'] = $avatarPath;
                }
            }
            
            $result = $this->userModel->update($userId, $updateData);
            
            if ($result) {
                // Обновляем сессию
                $_SESSION['user']['username'] = $updateData['username'];
                $_SESSION['user']['email'] = $updateData['email'];
                
                $this->json([
                    'success' => true,
                    'message' => 'Профиль успешно обновлен',
                    'redirect' => '/profile'
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Ошибка при обновлении профиля'], 500);
            }
            
        } catch (\Exception $e) {
            error_log("Error updating profile: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка при обновлении профиля'], 500);
        }
    }

    public function myProducts(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $page = max(1, intval($input['page'] ?? 1));
        $perPage = min(50, max(10, intval($input['per_page'] ?? 20)));
        
        $userId = $_SESSION['user']['id'];
        
        // Получаем товары пользователя
        $products = $this->productModel->findByUser($userId, $page, $perPage);
        $totalProducts = $this->getProductsCount($userId);
        $totalPages = ceil($totalProducts / $perPage);
        
        // Статистика товаров
        $productStats = $this->getProductStats($userId);
        
        $this->view('user/my-products', [
            'title' => 'Мои товары',
            'products' => $products,
            'stats' => $productStats,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_products' => $totalProducts,
                'per_page' => $perPage
            ]
        ]);
    }

    public function myPurchases(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $page = max(1, intval($input['page'] ?? 1));
        $perPage = min(50, max(10, intval($input['per_page'] ?? 20)));
        
        $userId = $_SESSION['user']['id'];
        
        // Получаем покупки пользователя
        $purchases = $this->getPurchases($userId, $page, $perPage);
        $totalPurchases = $this->getPurchasesCount($userId);
        $totalPages = ceil($totalPurchases / $perPage);
        
        // Статистика покупок
        $purchaseStats = $this->getPurchaseStats($userId);
        
        $this->view('user/my-purchases', [
            'title' => 'Мои покупки',
            'purchases' => $purchases,
            'stats' => $purchaseStats,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_purchases' => $totalPurchases,
                'per_page' => $perPage
            ]
        ]);
    }

    public function myFavorites(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $page = max(1, intval($input['page'] ?? 1));
        $perPage = min(50, max(10, intval($input['per_page'] ?? 20)));
        
        $userId = $_SESSION['user']['id'];
        
        // Получаем избранное пользователя
        $favorites = $this->favoriteModel->getUserFavorites($userId, $page, $perPage);
        $totalFavorites = $this->favoriteModel->getUserFavoritesCount($userId);
        $totalPages = ceil($totalFavorites / $perPage);
        
        $this->view('user/my-favorites', [
            'title' => 'Мое избранное',
            'favorites' => $favorites,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_favorites' => $totalFavorites,
                'per_page' => $perPage
            ]
        ]);
    }

    public function show(string $id): void
    {
        $userId = intval($id);
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $this->show404();
            return;
        }
        
        // Получаем публичные товары пользователя
        $products = $this->productModel->findAll(1, 12, ['user_id' => $userId]);
        
        // Статистика продавца
        $sellerStats = $this->getSellerStats($userId);
        
        // Отзывы о продавце
        $reviews = $this->getSellerReviews($userId, 1, 5);
        
        $this->view('user/show', [
            'title' => 'Профиль ' . $user['username'],
            'user' => $user,
            'products' => $products,
            'stats' => $sellerStats,
            'reviews' => $reviews,
            'canMessage' => isset($_SESSION['user']) && $_SESSION['user']['id'] != $userId
        ]);
    }

    public function settings(): void
    {
        $this->requireAuth();
        
        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $this->redirect('/login');
            return;
        }
        
        $this->view('user/settings', [
            'title' => 'Настройки аккаунта',
            'user' => $user,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function updatePassword(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $userId = $_SESSION['user']['id'];
        
        // Проверяем CSRF токен
        if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Неверный токен безопасности'], 400);
        }
        
        // Валидация паролей
        $errors = [];
        
        if (empty($input['current_password'])) {
            $errors['current_password'] = 'Введите текущий пароль';
        }
        
        if (empty($input['new_password'])) {
            $errors['new_password'] = 'Введите новый пароль';
        } elseif (strlen($input['new_password']) < 6) {
            $errors['new_password'] = 'Пароль должен содержать минимум 6 символов';
        }
        
        if ($input['new_password'] !== $input['confirm_password']) {
            $errors['confirm_password'] = 'Пароли не совпадают';
        }
        
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
        }
        
        try {
            // Проверяем текущий пароль
            $user = $this->userModel->findById($userId);
            if (!password_verify($input['current_password'], $user['password'])) {
                $this->json(['success' => false, 'errors' => ['current_password' => 'Неверный текущий пароль']], 400);
            }
            
            // Обновляем пароль
            $result = $this->userModel->updatePassword($userId, $input['new_password']);
            
            if ($result) {
                $this->json([
                    'success' => true,
                    'message' => 'Пароль успешно изменен'
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Ошибка при изменении пароля'], 500);
            }
            
        } catch (\Exception $e) {
            error_log("Error updating password: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка при изменении пароля'], 500);
        }
    }

    private function validateProfileData(array $data): array
    {
        $errors = [];
        
        // Валидация имени пользователя
        if (empty($data['username'])) {
            $errors['username'] = 'Имя пользователя обязательно';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Имя пользователя должно содержать минимум 3 символа';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors['username'] = 'Имя пользователя может содержать только буквы, цифры и подчеркивания';
        }
        
        // Валидация email
        if (empty($data['email'])) {
            $errors['email'] = 'Email обязателен';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Неверный формат email';
        }
        
        // Проверяем уникальность username и email
        $currentUserId = $_SESSION['user']['id'];
        $existingUser = $this->userModel->findByUsername($data['username']);
        if ($existingUser && $existingUser['id'] != $currentUserId) {
            $errors['username'] = 'Это имя пользователя уже занято';
        }
        
        $existingUser = $this->userModel->findByEmail($data['email']);
        if ($existingUser && $existingUser['id'] != $currentUserId) {
            $errors['email'] = 'Этот email уже используется';
        }
        
        return $errors;
    }

    private function handleAvatarUpload(): ?string
    {
        $uploadPath = $this->config['upload']['upload_path'];
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $file = $_FILES['avatar'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Проверки
        if ($file['size'] > $maxSize) {
            return null;
        }
        
        if (!in_array($fileExt, $allowedTypes)) {
            return null;
        }
        
        // Генерируем уникальное имя файла
        $fileName = 'avatar_' . $_SESSION['user']['id'] . '_' . time() . '.' . $fileExt;
        $filePath = $uploadPath . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return $fileName;
        }
        
        return null;
    }

    private function getProductsCount(int $userId): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE user_id = ?");
            $stmt->execute([$userId]);
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getPurchasesCount(int $userId): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM purchases WHERE buyer_id = ?");
            $stmt->execute([$userId]);
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getReviewsCount(int $userId): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE reviewer_id = ?");
            $stmt->execute([$userId]);
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getProductStats(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(views) as total_views,
                    AVG(rating) as avg_rating
                FROM products 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getPurchases(int $userId, int $page, int $perPage): array
    {
        try {
            $offset = ($page - 1) * $perPage;
            $stmt = $this->db->prepare("
                SELECT p.*, pr.title, pr.price, pr.images, u.username as seller_name
                FROM purchases p
                JOIN products pr ON p.product_id = pr.id
                JOIN users u ON p.seller_id = u.id
                WHERE p.buyer_id = ?
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getPurchaseStats(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(total_amount) as total_spent
                FROM purchases 
                WHERE buyer_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getSellerStats(int $userId): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(DISTINCT p.id) as products_count,
                    COUNT(DISTINCT pu.id) as sales_count,
                    SUM(pu.total_amount) as total_earned,
                    AVG(r.rating) as avg_rating,
                    COUNT(DISTINCT r.id) as reviews_count
                FROM users u
                LEFT JOIN products p ON u.id = p.user_id AND p.status = 'active'
                LEFT JOIN purchases pu ON u.id = pu.seller_id AND pu.status = 'completed'
                LEFT JOIN reviews r ON u.id = r.seller_id AND r.status = 'published'
                WHERE u.id = ?
                GROUP BY u.id
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch() ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getSellerReviews(int $userId, int $page, int $perPage): array
    {
        try {
            $offset = ($page - 1) * $perPage;
            $stmt = $this->db->prepare("
                SELECT r.*, u.username as reviewer_name, p.title as product_title
                FROM reviews r
                JOIN users u ON r.reviewer_id = u.id
                JOIN products p ON r.product_id = p.id
                WHERE r.seller_id = ? AND r.status = 'published'
                ORDER BY r.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$userId, $perPage, $offset]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }
}