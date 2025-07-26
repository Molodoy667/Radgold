<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    private Favorite $favoriteModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->favoriteModel = new Favorite($db);
    }

    public function add(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $productId = intval($input['product_id'] ?? 0);
        
        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Неверный ID товара'], 400);
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            $result = $this->favoriteModel->add($userId, $productId);
            
            if ($result) {
                $this->json([
                    'success' => true,
                    'message' => 'Товар добавлен в избранное',
                    'in_favorites' => true
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Товар не найден или уже в избранном'
                ], 400);
            }
            
        } catch (\Exception $e) {
            error_log("Error adding to favorites: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка при добавлении в избранное'], 500);
        }
    }

    public function remove(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $productId = intval($input['product_id'] ?? 0);
        
        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Неверный ID товара'], 400);
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            $result = $this->favoriteModel->remove($userId, $productId);
            
            $this->json([
                'success' => true,
                'message' => 'Товар удален из избранного',
                'in_favorites' => false
            ]);
            
        } catch (\Exception $e) {
            error_log("Error removing from favorites: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка при удалении из избранного'], 500);
        }
    }

    public function toggle(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $productId = intval($input['product_id'] ?? 0);
        
        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Неверный ID товара'], 400);
        }
        
        $userId = $_SESSION['user']['id'];
        
        try {
            $wasInFavorites = $this->favoriteModel->isFavorite($userId, $productId);
            $result = $this->favoriteModel->toggle($userId, $productId);
            
            if ($result) {
                $inFavorites = !$wasInFavorites;
                $message = $inFavorites ? 'Товар добавлен в избранное' : 'Товар удален из избранного';
                
                $this->json([
                    'success' => true,
                    'message' => $message,
                    'in_favorites' => $inFavorites
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Товар не найден'
                ], 404);
            }
            
        } catch (\Exception $e) {
            error_log("Error toggling favorite: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка при работе с избранным'], 500);
        }
    }

    public function index(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $page = max(1, intval($input['page'] ?? 1));
        $perPage = min(50, max(10, intval($input['per_page'] ?? 20)));
        
        $userId = $_SESSION['user']['id'];
        
        try {
            $favorites = $this->favoriteModel->getUserFavorites($userId, $page, $perPage);
            $totalFavorites = $this->favoriteModel->getUserFavoritesCount($userId);
            $totalPages = ceil($totalFavorites / $perPage);
            
            $this->view('user/favorites', [
                'title' => 'Мое избранное',
                'favorites' => $favorites,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_items' => $totalFavorites,
                    'per_page' => $perPage
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log("Error loading favorites: " . $e->getMessage());
            $this->view('user/favorites', [
                'title' => 'Мое избранное',
                'favorites' => [],
                'error' => 'Ошибка при загрузке избранного'
            ]);
        }
    }

    public function clear(): void
    {
        $this->requireAuth();
        
        $userId = $_SESSION['user']['id'];
        
        try {
            $result = $this->favoriteModel->clearUserFavorites($userId);
            
            $this->json([
                'success' => true,
                'message' => 'Избранное очищено'
            ]);
            
        } catch (\Exception $e) {
            error_log("Error clearing favorites: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка при очистке избранного'], 500);
        }
    }

    public function check(): void
    {
        $this->requireAuth();
        
        $input = $this->getInput();
        $productIds = $input['product_ids'] ?? [];
        
        if (!is_array($productIds)) {
            $productIds = [$productIds];
        }
        
        $userId = $_SESSION['user']['id'];
        $favorites = [];
        
        try {
            foreach ($productIds as $productId) {
                $productId = intval($productId);
                if ($productId > 0) {
                    $favorites[$productId] = $this->favoriteModel->isFavorite($userId, $productId);
                }
            }
            
            $this->json([
                'success' => true,
                'favorites' => $favorites
            ]);
            
        } catch (\Exception $e) {
            error_log("Error checking favorites: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка при проверке избранного'], 500);
        }
    }

    public function stats(): void
    {
        $this->requireAuth();
        
        $userId = $_SESSION['user']['id'];
        
        try {
            $totalFavorites = $this->favoriteModel->getUserFavoritesCount($userId);
            $recentFavorites = $this->favoriteModel->getUserFavorites($userId, 1, 5);
            
            $this->json([
                'success' => true,
                'stats' => [
                    'total_favorites' => $totalFavorites,
                    'recent_favorites' => $recentFavorites
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log("Error getting favorite stats: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка при получении статистики'], 500);
        }
    }
}