<?php
namespace App\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Favorite;
use App\Models\Setting;
use App\Models\User;

class ProductController {
    
    public function index($db) {
        $products = Product::findAll($db);
        $games = Product::getGames($db);
        $types = Product::getTypes($db);
        require_once __DIR__ . '/../views/products/index.php';
    }

    public function show($id, $db) {
        $product = Product::findById($id, $db);
        if (!$product) {
            header('HTTP/1.0 404 Not Found');
            echo '<h1>Товар не найден</h1>';
            return;
        }

        // Увеличиваем счетчик просмотров
        Product::incrementViews($id, $db);

        // Проверяем, в избранном ли товар
        $isFavorite = false;
        if (isset($_SESSION['user'])) {
            $isFavorite = Favorite::isFavorite($_SESSION['user']['id'], $id, $db);
        }

        require_once __DIR__ . '/../views/products/show.php';
    }

    public function filter($db) {
        $filters = [
            'game' => $_GET['game'] ?? '',
            'type' => $_GET['type'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $products = Product::filter($filters, $db);
        echo json_encode($products);
    }

    public function create($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productData = [
                'user_id' => $_SESSION['user']['id'],
                'type' => $_POST['type'],
                'game' => $_POST['game'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'currency' => $_POST['currency'] ?? 'RUB'
            ];

            if (Product::create($productData, $db)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при создании товара']);
            }
        } else {
            $games = Product::getGames($db);
            $types = Product::getTypes($db);
            require_once __DIR__ . '/../views/products/create.php';
        }
    }

    public function buy($db) {
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'error' => 'Не авторизован']);
            return;
        }

        $productId = $_POST['product_id'] ?? 0;
        $product = Product::findById($productId, $db);

        if (!$product) {
            echo json_encode(['success' => false, 'error' => 'Товар не найден']);
            return;
        }

        if ($product['status'] !== 'active') {
            echo json_encode(['success' => false, 'error' => 'Товар недоступен для покупки']);
            return;
        }

        if ($product['user_id'] == $_SESSION['user']['id']) {
            echo json_encode(['success' => false, 'error' => 'Нельзя купить свой товар']);
            return;
        }

        // Проверяем баланс покупателя
        if ($_SESSION['user']['balance'] < $product['price']) {
            echo json_encode(['success' => false, 'error' => 'Недостаточно средств на балансе']);
            return;
        }

        // Получаем комиссию
        $commissionPercent = Setting::get('commission_percent', $db, 5);
        $commission = ($product['price'] * $commissionPercent) / 100;

        // Создаем покупку
        $purchaseData = [
            'buyer_id' => $_SESSION['user']['id'],
            'seller_id' => $product['user_id'],
            'product_id' => $productId,
            'price' => $product['price'],
            'currency' => $product['currency'],
            'commission' => $commission
        ];

        if (Purchase::create($purchaseData, $db)) {
            // Списываем деньги с покупателя
            User::updateBalance($_SESSION['user']['id'], -$product['price'], $db);
            
            // Зачисляем деньги продавцу (за вычетом комиссии)
            $sellerAmount = $product['price'] - $commission;
            User::updateBalance($product['user_id'], $sellerAmount, $db);
            
            // Обновляем статус товара
            Product::updateStatus($productId, 'sold', $db);
            
            // Обновляем сессию пользователя
            $_SESSION['user']['balance'] -= $product['price'];
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при создании покупки']);
        }
    }
}
