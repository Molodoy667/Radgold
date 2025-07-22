<?php
require_once '../core/config.php';
require_once '../core/database.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$query = sanitize($input['query'] ?? '');
$limit = intval($input['limit'] ?? 10);

if (strlen($query) < 2) {
    jsonResponse(['success' => false, 'message' => 'Query too short']);
}

try {
    $db = Database::getInstance();
    $searchQuery = "%{$query}%";
    
    // Пошук в оголошеннях
    $sql = "
        SELECT 
            'ad' as type,
            id,
            title,
            description,
            image,
            CONCAT('" . SITE_URL . "/ad/', id) as url
        FROM ads 
        WHERE status = 'active' 
        AND (title LIKE ? OR description LIKE ?)
        
        UNION ALL
        
        SELECT 
            'page' as type,
            id,
            title,
            content as description,
            NULL as image,
            CONCAT('" . SITE_URL . "/', slug) as url
        FROM pages 
        WHERE status = 'published' 
        AND (title LIKE ? OR content LIKE ?)
        
        ORDER BY title
        LIMIT ?
    ";
    
    $result = $db->query($sql, [$searchQuery, $searchQuery, $searchQuery, $searchQuery, $limit]);
    $results = [];
    
    while ($row = $result->fetch_assoc()) {
        $results[] = [
            'type' => $row['type'],
            'id' => $row['id'],
            'title' => $row['title'],
            'description' => truncateText($row['description'], 150),
            'image' => $row['image'] ? SITE_URL . '/images/uploads/' . $row['image'] : null,
            'url' => $row['url']
        ];
    }
    
    jsonResponse(['success' => true, 'data' => $results]);
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}
?>
