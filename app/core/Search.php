<?php
namespace App\Core;

class Search {
    private static $db;
    
    public static function init() {
        if (!self::$db) {
            self::$db = Router::getDb();
        }
    }
    
    public static function searchProducts($query, $filters = [], $page = 1, $perPage = 20) {
        self::init();
        
        $qb = new QueryBuilder('products');
        $qb->select('products.*, users.login as seller_name, COUNT(reviews.id) as review_count, AVG(reviews.rating) as avg_rating')
           ->leftJoin('users', 'products.user_id', '=', 'users.id')
           ->leftJoin('reviews', 'products.id', '=', 'reviews.product_id');
        
        // Поиск по названию и описанию
        if (!empty($query)) {
            $qb->where('(products.title LIKE ? OR products.description LIKE ? OR products.game LIKE ?)', [
                '%' . $query . '%',
                '%' . $query . '%',
                '%' . $query . '%'
            ]);
        }
        
        // Фильтры
        if (!empty($filters['type'])) {
            $qb->where('products.type', $filters['type']);
        }
        
        if (!empty($filters['game'])) {
            $qb->where('products.game', $filters['game']);
        }
        
        if (!empty($filters['min_price'])) {
            $qb->where('products.price', '>=', $filters['min_price']);
        }
        
        if (!empty($filters['max_price'])) {
            $qb->where('products.price', '<=', $filters['max_price']);
        }
        
        if (!empty($filters['status'])) {
            $qb->where('products.status', $filters['status']);
        }
        
        $qb->where('products.status', 'active')
           ->groupBy('products.id')
           ->orderBy('products.created_at', 'DESC');
        
        return $qb->paginate($perPage, $page);
    }
    
    public static function autocomplete($query, $type = 'products', $limit = 10) {
        self::init();
        
        switch ($type) {
            case 'products':
                return self::autocompleteProducts($query, $limit);
            case 'games':
                return self::autocompleteGames($query, $limit);
            case 'users':
                return self::autocompleteUsers($query, $limit);
            default:
                return [];
        }
    }
    
    private static function autocompleteProducts($query, $limit) {
        $sql = "SELECT DISTINCT title, game, type FROM products 
                WHERE (title LIKE ? OR game LIKE ?) AND status = 'active' 
                ORDER BY 
                    CASE 
                        WHEN title LIKE ? THEN 1 
                        WHEN title LIKE ? THEN 2 
                        ELSE 3 
                    END,
                    title ASC 
                LIMIT ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            $query . '%',
            $query . '%',
            $query . '%',
            '%' . $query . '%',
            $limit
        ]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private static function autocompleteGames($query, $limit) {
        $sql = "SELECT DISTINCT game, COUNT(*) as count FROM products 
                WHERE game LIKE ? AND status = 'active' 
                GROUP BY game 
                ORDER BY count DESC, game ASC 
                LIMIT ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$query . '%', $limit]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    private static function autocompleteUsers($query, $limit) {
        $sql = "SELECT id, login, email FROM users 
                WHERE (login LIKE ? OR email LIKE ?) AND status = 'active' 
                ORDER BY 
                    CASE 
                        WHEN login LIKE ? THEN 1 
                        WHEN login LIKE ? THEN 2 
                        ELSE 3 
                    END,
                    login ASC 
                LIMIT ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            $query . '%',
            $query . '%',
            $query . '%',
            '%' . $query . '%',
            $limit
        ]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function getPopularGames($limit = 10) {
        self::init();
        
        $sql = "SELECT game, COUNT(*) as product_count, AVG(price) as avg_price 
                FROM products 
                WHERE status = 'active' 
                GROUP BY game 
                ORDER BY product_count DESC 
                LIMIT ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function getRecentSearches($userId, $limit = 10) {
        self::init();
        
        $sql = "SELECT query, COUNT(*) as count, MAX(created_at) as last_search 
                FROM search_logs 
                WHERE user_id = ? 
                GROUP BY query 
                ORDER BY last_search DESC 
                LIMIT ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$userId, $limit]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function logSearch($userId, $query, $results = 0) {
        self::init();
        
        $sql = "INSERT INTO search_logs (user_id, query, results, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            $userId,
            $query,
            $results,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
    
    public static function getSearchSuggestions($query) {
        self::init();
        
        $suggestions = [];
        
        // Популярные поиски
        $sql = "SELECT query, COUNT(*) as count 
                FROM search_logs 
                WHERE query LIKE ? 
                GROUP BY query 
                ORDER BY count DESC 
                LIMIT 5";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute(['%' . $query . '%']);
        $suggestions['popular'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Похожие игры
        $sql = "SELECT DISTINCT game 
                FROM products 
                WHERE game LIKE ? AND status = 'active' 
                ORDER BY game ASC 
                LIMIT 5";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$query . '%']);
        $suggestions['games'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $suggestions;
    }
    
    public static function buildSearchIndex() {
        self::init();
        
        // Создаем индекс для поиска по продуктам
        $sql = "ALTER TABLE products ADD FULLTEXT INDEX ft_search (title, description, game)";
        
        try {
            $stmt = self::$db->prepare($sql);
            $stmt->execute();
            return true;
        } catch (\PDOException $e) {
            // Индекс уже существует
            return false;
        }
    }
    
    public static function fullTextSearch($query, $page = 1, $perPage = 20) {
        self::init();
        
        $qb = new QueryBuilder('products');
        $qb->select('products.*, users.login as seller_name, MATCH(products.title, products.description, products.game) AGAINST(? IN BOOLEAN MODE) as relevance')
           ->leftJoin('users', 'products.user_id', '=', 'users.id')
           ->where('MATCH(products.title, products.description, products.game) AGAINST(? IN BOOLEAN MODE)', [$query, $query])
           ->where('products.status', 'active')
           ->orderBy('relevance', 'DESC')
           ->orderBy('products.created_at', 'DESC');
        
        return $qb->paginate($perPage, $page);
    }
}