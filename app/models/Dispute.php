<?php
namespace App\Models;

class Dispute {
    public $id;
    public $purchase_id;
    public $user_id;
    public $type;
    public $subject;
    public $description;
    public $status;
    public $priority;
    public $admin_id;
    public $admin_response;
    public $resolution;
    public $created_at;
    public $updated_at;

    public static function create($data, $db) {
        $stmt = $db->prepare("INSERT INTO disputes (purchase_id, user_id, type, subject, description, status, priority) 
                             VALUES (?, ?, ?, ?, ?, 'open', ?)");
        return $stmt->execute([
            $data['purchase_id'],
            $data['user_id'],
            $data['type'],
            $data['subject'],
            $data['description'],
            $data['priority'] ?? 'medium'
        ]);
    }

    public static function findById($id, $db) {
        $stmt = $db->prepare("SELECT d.*, p.product_id, pr.title as product_title, pr.game,
                             u.login as user_login, a.login as admin_login
                             FROM disputes d
                             JOIN purchases p ON d.purchase_id = p.id
                             JOIN products pr ON p.product_id = pr.id
                             JOIN users u ON d.user_id = u.id
                             LEFT JOIN users a ON d.admin_id = a.id
                             WHERE d.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function findByUser($userId, $db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT d.*, p.product_id, pr.title as product_title, pr.game,
                             a.login as admin_login
                             FROM disputes d
                             JOIN purchases p ON d.purchase_id = p.id
                             JOIN products pr ON p.product_id = pr.id
                             LEFT JOIN users a ON d.admin_id = a.id
                             WHERE d.user_id = ?
                             ORDER BY d.created_at DESC
                             LIMIT ? OFFSET ?");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getOpenDisputes($db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT d.*, p.product_id, pr.title as product_title,
                             u.login as user_login, a.login as admin_login
                             FROM disputes d
                             JOIN purchases p ON d.purchase_id = p.id
                             JOIN products pr ON p.product_id = pr.id
                             JOIN users u ON d.user_id = u.id
                             LEFT JOIN users a ON d.admin_id = a.id
                             WHERE d.status IN ('open', 'in_progress')
                             ORDER BY d.priority DESC, d.created_at ASC
                             LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function updateStatus($id, $status, $adminId = null, $db) {
        $stmt = $db->prepare("UPDATE disputes SET status = ?, admin_id = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $adminId, $id]);
    }

    public static function addAdminResponse($id, $response, $adminId, $db) {
        $stmt = $db->prepare("UPDATE disputes SET admin_response = ?, admin_id = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$response, $adminId, $id]);
    }

    public static function resolve($id, $resolution, $adminId, $db) {
        $stmt = $db->prepare("UPDATE disputes SET resolution = ?, admin_id = ?, status = 'resolved', updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$resolution, $adminId, $id]);
    }

    public static function getStats($db) {
        $stmt = $db->prepare("SELECT 
                                COUNT(*) as total_disputes,
                                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_disputes,
                                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_disputes,
                                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_disputes,
                                SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_disputes,
                                SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent_disputes
                             FROM disputes");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function canCreateDispute($userId, $purchaseId, $db) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM disputes WHERE user_id = ? AND purchase_id = ?");
        $stmt->execute([$userId, $purchaseId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }
}