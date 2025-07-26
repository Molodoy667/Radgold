<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\User;

class PurchaseController extends Controller
{
    private Product $productModel;
    private User $userModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->productModel = new Product($db);
        $this->userModel = new User($db);
    }

    public function create(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        
        if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Неверный токен безопасности'], 400);
            return;
        }

        $productId = intval($input['product_id'] ?? 0);
        $quantity = max(1, intval($input['quantity'] ?? 1));

        $product = $this->productModel->findById($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Товар не найден'], 404);
            return;
        }

        if ($product['status'] !== 'active') {
            $this->json(['success' => false, 'message' => 'Товар недоступен для покупки'], 400);
            return;
        }

        $buyer = $_SESSION['user'];
        if ($buyer['id'] === $product['user_id']) {
            $this->json(['success' => false, 'message' => 'Нельзя купить собственный товар'], 400);
            return;
        }

        $totalAmount = $product['price'] * $quantity;
        $commission = $totalAmount * ($this->config['commission']['default_rate'] ?? 0.05);

        try {
            $this->db->beginTransaction();

            // Создаем запись покупки
            $purchaseId = $this->createPurchaseRecord([
                'buyer_id' => $buyer['id'],
                'seller_id' => $product['user_id'],
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product['price'],
                'commission' => $commission,
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            // Создаем транзакцию
            $this->createTransaction([
                'user_id' => $buyer['id'],
                'purchase_id' => $purchaseId,
                'type' => 'purchase',
                'amount' => -$totalAmount,
                'status' => 'pending',
                'description' => "Покупка: {$product['title']}"
            ]);

            $this->db->commit();

            $this->json([
                'success' => true,
                'message' => 'Покупка создана успешно',
                'purchase_id' => $purchaseId,
                'redirect' => "/purchases/{$purchaseId}"
            ]);

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("Purchase creation error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка создания покупки'], 500);
        }
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        
        $purchaseId = intval($id);
        $purchase = $this->getPurchaseById($purchaseId);
        
        if (!$purchase) {
            $this->view('errors/404');
            return;
        }

        $user = $_SESSION['user'];
        if ($purchase['buyer_id'] !== $user['id'] && $purchase['seller_id'] !== $user['id']) {
            $this->view('errors/403');
            return;
        }

        $this->view('purchases/show', [
            'title' => 'Детали покупки',
            'purchase' => $purchase
        ]);
    }

    private function createPurchaseRecord(array $data): int
    {
        $sql = "INSERT INTO purchases (buyer_id, seller_id, product_id, quantity, price, commission, total_amount, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['buyer_id'],
            $data['seller_id'],
            $data['product_id'],
            $data['quantity'],
            $data['price'],
            $data['commission'],
            $data['total_amount'],
            $data['status']
        ]);

        return $this->db->lastInsertId();
    }

    private function createTransaction(array $data): int
    {
        $sql = "INSERT INTO transactions (user_id, purchase_id, type, amount, status, description, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['user_id'],
            $data['purchase_id'],
            $data['type'],
            $data['amount'],
            $data['status'],
            $data['description']
        ]);

        return $this->db->lastInsertId();
    }

    private function getPurchaseById(int $id): ?array
    {
        $sql = "SELECT p.*, 
                       prod.title as product_title,
                       prod.description as product_description,
                       prod.images as product_images,
                       buyer.username as buyer_username,
                       buyer.email as buyer_email,
                       seller.username as seller_username,
                       seller.email as seller_email
                FROM purchases p
                JOIN products prod ON p.product_id = prod.id
                JOIN users buyer ON p.buyer_id = buyer.id
                JOIN users seller ON p.seller_id = seller.id
                WHERE p.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch() ?: null;
    }
}