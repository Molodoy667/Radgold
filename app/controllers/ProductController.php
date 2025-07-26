<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\User;

class ProductController extends Controller
{
    private Product $productModel;
    private User $userModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->productModel = new Product($db);
        $this->userModel = new User($db);
    }

    public function index(): void
    {
        // Главная страница с рекомендуемыми товарами
        $userId = $_SESSION['user']['id'] ?? null;
        $user = $_SESSION['user'] ?? null;
        
        $this->view('products/index', [
            'title' => 'Главная страница',
            'user' => $user,
            'totalUsers' => $this->getTotalUsers(),
            'featuredProducts' => $this->productModel->getFeatured(6),
            'recommendedProducts' => $this->productModel->getRecommended($userId, 6),
            'popularGames' => $this->productModel->getGames(),
            'stats' => $this->getMarketStats()
        ]);
    }

    public function catalog(): void
    {
        $input = $this->getInput();
        
        // Параметры пагинации
        $page = max(1, intval($input['page'] ?? 1));
        $perPage = min(50, max(10, intval($input['per_page'] ?? 20)));
        
        // Фильтры
        $filters = [
            'game' => $input['game'] ?? '',
            'type' => $input['type'] ?? '',
            'min_price' => $input['min_price'] ?? '',
            'max_price' => $input['max_price'] ?? '',
            'search' => $input['search'] ?? '',
            'sort' => $input['sort'] ?? 'newest'
        ];
        
        // Получаем товары и статистику
        $products = $this->productModel->findAll($page, $perPage, $filters);
        $totalProducts = $this->productModel->getCount($filters);
        $totalPages = ceil($totalProducts / $perPage);
        
        // Для AJAX запросов возвращаем JSON
        if (!empty($input['ajax'])) {
            $this->json([
                'products' => $products,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_products' => $totalProducts,
                    'per_page' => $perPage
                ]
            ]);
        }
        
        $this->view('products/catalog', [
            'title' => 'Каталог товаров',
            'user' => $_SESSION['user'] ?? null,
            'products' => $products,
            'filters' => $filters,
            'games' => $this->productModel->getGames(),
            'types' => $this->productModel->getTypes(),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_products' => $totalProducts,
                'per_page' => $perPage
            ]
        ]);
    }

    public function show(string $id): void
    {
        $productId = intval($id);
        $product = $this->productModel->findById($productId);
        
        if (!$product) {
            $this->redirect('/catalog');
            return;
        }
        
        // Увеличиваем счетчик просмотров
        $this->productModel->incrementViews($productId);
        
        // Проверяем, в избранном ли товар у текущего пользователя
        $isFavorite = false;
        if (isset($_SESSION['user'])) {
            $favoriteModel = new \App\Models\Favorite($this->db);
            $isFavorite = $favoriteModel->isFavorite($_SESSION['user']['id'], $productId);
        }
        
        // Получаем похожие товары
        $similarProducts = $this->productModel->findAll(1, 4, [
            'game' => $product['game'],
            'type' => $product['type']
        ]);
        
        // Исключаем текущий товар из похожих
        $similarProducts = array_filter($similarProducts, function($p) use ($productId) {
            return $p['id'] != $productId;
        });
        
        $this->view('products/show', [
            'title' => $product['title'],
            'user' => $_SESSION['user'] ?? null,
            'product' => $product,
            'isFavorite' => $isFavorite,
            'similarProducts' => array_slice($similarProducts, 0, 3),
            'reviews' => $this->getProductReviews($productId),
            'canReview' => $this->canUserReview($productId)
        ]);
    }

    public function createForm(): void
    {
        $this->requireAuth();
        
        $this->view('products/create', [
            'title' => 'Добавить товар',
            'user' => $_SESSION['user'] ?? null,
            'games' => $this->config['games'],
            'productTypes' => $this->config['product_types'],
            'currencies' => $this->config['currencies'],
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $input = $this->getInput();
        
        // Проверяем CSRF токен
        if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Неверный токен безопасности'], 400);
        }
        
        // Валидация данных
        $errors = $this->validateProductData($input);
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
        }
        
        try {
            // Обработка загруженных изображений
            $images = $this->handleImageUpload();
            
            $productData = [
                'user_id' => $_SESSION['user']['id'],
                'type' => $input['type'],
                'game' => $input['game'],
                'title' => $input['title'],
                'description' => $input['description'],
                'short_description' => $input['short_description'] ?? substr($input['description'], 0, 200),
                'price' => floatval($input['price']),
                'currency' => $input['currency'] ?? 'RUB',
                'original_price' => !empty($input['original_price']) ? floatval($input['original_price']) : null,
                'delivery_info' => $input['delivery_info'] ?? '',
                'delivery_time' => $input['delivery_time'] ?? '',
                'auto_delivery' => !empty($input['auto_delivery']),
                'instant_delivery' => !empty($input['instant_delivery']),
                'warranty_days' => intval($input['warranty_days'] ?? 0),
                'stock_quantity' => intval($input['stock_quantity'] ?? 1),
                'images' => $images,
                'specifications' => $this->parseSpecifications($input),
                'tags' => $this->parseTags($input['tags'] ?? ''),
                'status' => 'pending', // Требует модерации
                'visibility' => 'public'
            ];
            
            $productId = $this->productModel->create($productData);
            
            $this->json([
                'success' => true,
                'message' => 'Товар успешно добавлен и отправлен на модерацию',
                'product_id' => $productId,
                'redirect' => '/my-products'
            ]);
            
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Ошибка при создании товара'], 500);
        }
    }

    public function editForm(string $id): void
    {
        $this->requireAuth();
        $productId = intval($id);
        
        $product = $this->productModel->findById($productId);
        if (!$product || $product['user_id'] != $_SESSION['user']['id']) {
            $this->redirect('/my-products');
            return;
        }
        
        // Декодируем JSON поля
        $product['images'] = json_decode($product['images'] ?? '[]', true);
        $product['specifications'] = json_decode($product['specifications'] ?? '{}', true);
        $product['tags'] = json_decode($product['tags'] ?? '[]', true);
        
        $this->view('products/edit', [
            'title' => 'Редактировать товар',
            'user' => $_SESSION['user'] ?? null,
            'product' => $product,
            'games' => $this->config['games'],
            'productTypes' => $this->config['product_types'],
            'currencies' => $this->config['currencies'],
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $productId = intval($id);
        $input = $this->getInput();
        
        // Проверяем права доступа
        $product = $this->productModel->findById($productId);
        if (!$product || $product['user_id'] != $_SESSION['user']['id']) {
            $this->json(['success' => false, 'message' => 'Товар не найден'], 404);
        }
        
        // Проверяем CSRF токен
        if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Неверный токен безопасности'], 400);
        }
        
        // Валидация данных
        $errors = $this->validateProductData($input, $productId);
        if (!empty($errors)) {
            $this->json(['success' => false, 'errors' => $errors], 400);
        }
        
        try {
            $updateData = [
                'type' => $input['type'],
                'game' => $input['game'],
                'title' => $input['title'],
                'description' => $input['description'],
                'short_description' => $input['short_description'] ?? substr($input['description'], 0, 200),
                'price' => floatval($input['price']),
                'currency' => $input['currency'] ?? 'RUB',
                'original_price' => !empty($input['original_price']) ? floatval($input['original_price']) : null,
                'delivery_info' => $input['delivery_info'] ?? '',
                'delivery_time' => $input['delivery_time'] ?? '',
                'auto_delivery' => !empty($input['auto_delivery']),
                'instant_delivery' => !empty($input['instant_delivery']),
                'warranty_days' => intval($input['warranty_days'] ?? 0),
                'stock_quantity' => intval($input['stock_quantity'] ?? 1),
                'specifications' => $this->parseSpecifications($input),
                'tags' => $this->parseTags($input['tags'] ?? ''),
                'status' => 'pending' // Требует повторной модерации
            ];
            
            // Обработка новых изображений если есть
            $newImages = $this->handleImageUpload();
            if (!empty($newImages)) {
                $updateData['images'] = $newImages;
            }
            
            $this->productModel->update($productId, $updateData);
            
            $this->json([
                'success' => true,
                'message' => 'Товар успешно обновлен',
                'redirect' => '/my-products'
            ]);
            
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Ошибка при обновлении товара'], 500);
        }
    }

    public function delete(string $id): void
    {
        $this->requireAuth();
        $productId = intval($id);
        
        // Проверяем права доступа
        $product = $this->productModel->findById($productId);
        if (!$product || $product['user_id'] != $_SESSION['user']['id']) {
            $this->json(['success' => false, 'message' => 'Товар не найден'], 404);
        }
        
        try {
            $this->productModel->delete($productId);
            
            $this->json([
                'success' => true,
                'message' => 'Товар успешно удален'
            ]);
            
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Ошибка при удалении товара'], 500);
        }
    }

    private function getTotalUsers(): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
            $stmt->execute();
            $result = $stmt->fetch();
            return (int) $result['count'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getMarketStats(): array
    {
        $productStats = $this->productModel->getStats();
        $userStats = $this->userModel->getStats();
        
        return [
            'total_products' => $productStats['active_products'] ?? 0,
            'total_users' => $userStats['active_users'] ?? 0,
            'avg_price' => $productStats['avg_price'] ?? 0,
            'total_sales' => $userStats['total_sales'] ?? 0
        ];
    }

    private function validateProductData(array $data, int $productId = null): array
    {
        $errors = [];
        
        // Валидация обязательных полей
        if (empty($data['title'])) {
            $errors['title'] = 'Название товара обязательно';
        } elseif (strlen($data['title']) < 10) {
            $errors['title'] = 'Название должно содержать минимум 10 символов';
        } elseif (strlen($data['title']) > 255) {
            $errors['title'] = 'Название не должно превышать 255 символов';
        }
        
        if (empty($data['description'])) {
            $errors['description'] = 'Описание товара обязательно';
        } elseif (strlen($data['description']) < 50) {
            $errors['description'] = 'Описание должно содержать минимум 50 символов';
        }
        
        if (empty($data['price']) || floatval($data['price']) <= 0) {
            $errors['price'] = 'Цена должна быть больше нуля';
        } elseif (floatval($data['price']) > 1000000) {
            $errors['price'] = 'Цена не должна превышать 1,000,000';
        }
        
        if (empty($data['type'])) {
            $errors['type'] = 'Тип товара обязателен';
        } elseif (!array_key_exists($data['type'], $this->config['product_types'])) {
            $errors['type'] = 'Недопустимый тип товара';
        }
        
        if (empty($data['game'])) {
            $errors['game'] = 'Игра обязательна';
        } elseif (!array_key_exists($data['game'], $this->config['games'])) {
            $errors['game'] = 'Недопустимая игра';
        }
        
        return $errors;
    }

    private function handleImageUpload(): array
    {
        $images = [];
        
        if (!empty($_FILES['images'])) {
            $uploadPath = $this->config['upload']['upload_path'];
            $allowedTypes = $this->config['upload']['allowed_extensions'];
            $maxSize = $this->config['upload']['max_file_size'];
            
            // Создаем папку для загрузок если её нет
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if (empty($tmpName)) continue;
                
                $originalName = $_FILES['images']['name'][$key];
                $fileSize = $_FILES['images']['size'][$key];
                $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                
                // Проверки
                if ($fileSize > $maxSize) continue;
                if (!in_array($fileExt, $allowedTypes)) continue;
                
                // Генерируем уникальное имя файла
                $fileName = uniqid() . '_' . time() . '.' . $fileExt;
                $filePath = $uploadPath . $fileName;
                
                if (move_uploaded_file($tmpName, $filePath)) {
                    $images[] = $fileName;
                }
            }
        }
        
        return $images;
    }

    private function parseSpecifications(array $input): array
    {
        $specs = [];
        
        // Парсим характеристики из формы
        if (!empty($input['spec_keys']) && !empty($input['spec_values'])) {
            $keys = $input['spec_keys'];
            $values = $input['spec_values'];
            
            for ($i = 0; $i < count($keys); $i++) {
                if (!empty($keys[$i]) && !empty($values[$i])) {
                    $specs[trim($keys[$i])] = trim($values[$i]);
                }
            }
        }
        
        return $specs;
    }

    private function parseTags(string $tagsString): array
    {
        if (empty($tagsString)) {
            return [];
        }
        
        $tags = explode(',', $tagsString);
        $tags = array_map('trim', $tags);
        $tags = array_filter($tags);
        $tags = array_unique($tags);
        
        return array_values($tags);
    }

    private function getProductReviews(int $productId): array
    {
        try {
            $reviewModel = new \App\Models\Review($this->db);
            return $reviewModel->getByProduct($productId, 1, 10);
        } catch (\Exception $e) {
            return [];
        }
    }

    private function canUserReview(int $productId): bool
    {
        if (!isset($_SESSION['user'])) {
            return false;
        }
        
        try {
            // Проверяем, покупал ли пользователь этот товар
            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM purchases 
                WHERE buyer_id = ? AND product_id = ? AND status = 'completed'
            ");
            $stmt->execute([$_SESSION['user']['id'], $productId]);
            $hasPurchased = $stmt->fetchColumn() > 0;
            
            if (!$hasPurchased) {
                return false;
            }
            
            // Проверяем, не оставлял ли уже отзыв
            $stmt = $this->db->prepare("
                SELECT COUNT(*) 
                FROM reviews r
                JOIN purchases p ON r.purchase_id = p.id
                WHERE p.buyer_id = ? AND r.product_id = ?
            ");
            $stmt->execute([$_SESSION['user']['id'], $productId]);
            $hasReviewed = $stmt->fetchColumn() > 0;
            
            return !$hasReviewed;
            
        } catch (\Exception $e) {
            return false;
        }
    }
}