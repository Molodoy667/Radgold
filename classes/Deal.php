<?php

class Deal {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function createDeal($sellerId, $buyerId, $title, $description, $amount) {
        // Генерируем уникальный номер сделки
        $dealNumber = 'D' . date('Ymd') . rand(1000, 9999);
        
        // Проверяем уникальность номера
        while ($this->getDealByNumber($dealNumber)) {
            $dealNumber = 'D' . date('Ymd') . rand(1000, 9999);
        }
        
        // Получаем комиссию из настроек
        $settings = $this->getSettings();
        $commission = ($amount * $settings['commission_percent']) / 100;
        
        // Устанавливаем время истечения сделки
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . $settings['deal_timeout_hours'] . ' hours'));
        
        $dealId = $this->db->insert('deals', [
            'deal_number' => $dealNumber,
            'seller_id' => $sellerId,
            'buyer_id' => $buyerId,
            'title' => $title,
            'description' => $description,
            'amount' => $amount,
            'commission' => $commission,
            'status' => 'created',
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Добавляем системное сообщение
        $this->addDealMessage($dealId, 0, "Сделка #{$dealNumber} создана", true);
        
        return $dealId;
    }
    
    public function getDeal($dealId) {
        return $this->db->fetch(
            'SELECT * FROM deals WHERE id = :deal_id',
            ['deal_id' => $dealId]
        );
    }
    
    public function getDealByNumber($dealNumber) {
        return $this->db->fetch(
            'SELECT * FROM deals WHERE deal_number = :deal_number',
            ['deal_number' => $dealNumber]
        );
    }
    
    public function getUserDeals($userId, $status = null) {
        $sql = 'SELECT * FROM deals WHERE seller_id = :user_id OR buyer_id = :user_id';
        $params = ['user_id' => $userId];
        
        if ($status) {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        }
        
        $sql .= ' ORDER BY created_at DESC';
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function updateDealStatus($dealId, $status) {
        $this->db->update('deals', [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :deal_id', ['deal_id' => $dealId]);
        
        // Добавляем системное сообщение
        $statusMessages = [
            'paid' => 'Сделка оплачена покупателем',
            'confirmed' => 'Сделка подтверждена продавцом',
            'disputed' => 'Открыт спор по сделке',
            'completed' => 'Сделка успешно завершена',
            'cancelled' => 'Сделка отменена'
        ];
        
        if (isset($statusMessages[$status])) {
            $this->addDealMessage($dealId, 0, $statusMessages[$status], true);
        }
    }
    
    public function confirmDeal($dealId, $userId, $role) {
        $deal = $this->getDeal($dealId);
        if (!$deal) {
            return false;
        }
        
        if ($role === 'seller' && $deal['seller_id'] == $userId) {
            $this->db->update('deals', [
                'seller_confirmed' => true
            ], 'id = :deal_id', ['deal_id' => $dealId]);
            
            $this->addDealMessage($dealId, $userId, "Продавец подтвердил выполнение условий сделки", true);
        } elseif ($role === 'buyer' && $deal['buyer_id'] == $userId) {
            $this->db->update('deals', [
                'buyer_confirmed' => true
            ], 'id = :deal_id', ['deal_id' => $dealId]);
            
            $this->addDealMessage($dealId, $userId, "Покупатель подтвердил получение товара/услуги", true);
        }
        
        // Проверяем, подтвердили ли обе стороны
        $updatedDeal = $this->getDeal($dealId);
        if ($updatedDeal['seller_confirmed'] && $updatedDeal['buyer_confirmed']) {
            $this->completeDeal($dealId);
        }
        
        return true;
    }
    
    public function completeDeal($dealId) {
        $deal = $this->getDeal($dealId);
        if (!$deal || $deal['status'] !== 'confirmed') {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            // Обновляем статус сделки
            $this->updateDealStatus($dealId, 'completed');
            
            // Переводим средства продавцу (за вычетом комиссии)
            $sellerAmount = $deal['amount'] - $deal['commission'];
            $user = new User($this->db);
            $user->updateBalance($deal['seller_id'], $sellerAmount, 'add');
            
            // Увеличиваем счетчик сделок для обеих сторон
            $user->incrementDealsCount($deal['seller_id']);
            $user->incrementDealsCount($deal['buyer_id']);
            
            $this->db->commit();
            
            $this->addDealMessage($dealId, 0, "Сделка завершена. Средства переведены продавцу.", true);
            
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    public function cancelDeal($dealId, $reason = null) {
        $deal = $this->getDeal($dealId);
        if (!$deal) {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            // Обновляем статус
            $this->updateDealStatus($dealId, 'cancelled');
            
            // Если сделка была оплачена, возвращаем средства покупателю
            if ($deal['status'] === 'paid') {
                $user = new User($this->db);
                $user->updateBalance($deal['buyer_id'], $deal['amount'], 'add');
            }
            
            $this->db->commit();
            
            $message = "Сделка отменена";
            if ($reason) {
                $message .= ". Причина: " . $reason;
            }
            $this->addDealMessage($dealId, 0, $message, true);
            
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    public function createDispute($dealId, $userId, $reason) {
        $deal = $this->getDeal($dealId);
        if (!$deal) {
            return false;
        }
        
        // Проверяем, что пользователь является участником сделки
        if ($deal['seller_id'] != $userId && $deal['buyer_id'] != $userId) {
            return false;
        }
        
        $this->db->update('deals', [
            'status' => 'disputed',
            'dispute_reason' => $reason,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :deal_id', ['deal_id' => $dealId]);
        
        $this->addDealMessage($dealId, $userId, "Открыт спор. Причина: " . $reason, true);
        
        return true;
    }
    
    public function addDealMessage($dealId, $userId, $message, $isSystem = false) {
        return $this->db->insert('deal_messages', [
            'deal_id' => $dealId,
            'user_id' => $userId,
            'message' => $message,
            'is_system' => $isSystem,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    public function getDealMessages($dealId) {
        return $this->db->fetchAll('
            SELECT dm.*, u.first_name, u.last_name, u.username
            FROM deal_messages dm
            LEFT JOIN users u ON dm.user_id = u.telegram_id
            WHERE dm.deal_id = :deal_id
            ORDER BY dm.created_at ASC
        ', ['deal_id' => $dealId]);
    }
    
    public function getExpiredDeals() {
        return $this->db->fetchAll('
            SELECT * FROM deals 
            WHERE status IN ("created", "paid") 
            AND expires_at < NOW()
        ');
    }
    
    public function getActiveDeals() {
        return $this->db->fetchAll('
            SELECT * FROM deals 
            WHERE status IN ("created", "paid", "confirmed") 
            ORDER BY created_at DESC
        ');
    }
    
    public function getDealStats() {
        return $this->db->fetch('
            SELECT 
                COUNT(*) as total_deals,
                COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_deals,
                COUNT(CASE WHEN status = "disputed" THEN 1 END) as disputed_deals,
                COUNT(CASE WHEN status = "cancelled" THEN 1 END) as cancelled_deals,
                SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_volume,
                SUM(CASE WHEN status = "completed" THEN commission ELSE 0 END) as total_commission
            FROM deals
        ');
    }
    
    private function getSettings() {
        $settings = $this->db->fetchAll('SELECT setting_key, setting_value FROM bot_settings');
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        
        return $result;
    }
}