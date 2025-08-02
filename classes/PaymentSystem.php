<?php

class PaymentSystem {
    private $db;
    private $config;
    
    public function __construct($db, $config) {
        $this->db = $db;
        $this->config = $config;
    }
    
    public function createPayment($dealId, $userId, $amount, $method = 'sberbank') {
        // Создаем запись о платеже
        $paymentId = $this->db->insert('payments', [
            'deal_id' => $dealId,
            'user_id' => $userId,
            'amount' => $amount,
            'payment_method' => $method,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Генерируем инструкции для оплаты в зависимости от метода
        switch ($method) {
            case 'sberbank':
                return $this->createSberbankPayment($paymentId, $amount);
            case 'mir_card':
                return $this->createMirCardPayment($paymentId, $amount);
            case 'manual_card':
                return $this->createManualCardPayment($paymentId, $amount);
            default:
                throw new Exception('Неподдерживаемый метод оплаты');
        }
    }
    
    private function createSberbankPayment($paymentId, $amount) {
        // Получаем данные Сбербанка из настроек
        $sberbankData = $this->getPaymentSetting('sberbank_data');
        
        return [
            'payment_id' => $paymentId,
            'method' => 'sberbank',
            'instructions' => [
                'title' => '💳 Оплата через Сбербанк',
                'steps' => [
                    '1️⃣ Откройте приложение Сбербанк Онлайн',
                    '2️⃣ Выберите "Переводы" → "По номеру телефона"',
                    '3️⃣ Укажите номер: ' . ($sberbankData['phone'] ?? '+7XXXXXXXXXX'),
                    '4️⃣ Сумма: ' . number_format($amount, 2) . ' ₽',
                    '5️⃣ Комментарий: #' . $paymentId,
                    '6️⃣ Подтвердите перевод'
                ],
                'card_number' => $sberbankData['card_number'] ?? 'Не указан',
                'phone' => $sberbankData['phone'] ?? 'Не указан',
                'holder_name' => $sberbankData['holder_name'] ?? 'Не указан'
            ]
        ];
    }
    
    private function createMirCardPayment($paymentId, $amount) {
        // Получаем данные карты МИР из настроек
        $mirData = $this->getPaymentSetting('mir_card_data');
        
        return [
            'payment_id' => $paymentId,
            'method' => 'mir_card',
            'instructions' => [
                'title' => '💳 Оплата на карту МИР',
                'steps' => [
                    '1️⃣ Откройте банковское приложение',
                    '2️⃣ Выберите "Переводы на карту"',
                    '3️⃣ Номер карты: ' . ($mirData['card_number'] ?? 'XXXX XXXX XXXX XXXX'),
                    '4️⃣ Сумма: ' . number_format($amount, 2) . ' ₽',
                    '5️⃣ Назначение: Платеж #' . $paymentId,
                    '6️⃣ Подтвердите перевод'
                ],
                'card_number' => $mirData['card_number'] ?? 'Не указан',
                'holder_name' => $mirData['holder_name'] ?? 'Не указан',
                'bank_name' => $mirData['bank_name'] ?? 'Не указан'
            ]
        ];
    }
    
    private function createManualCardPayment($paymentId, $amount) {
        // Получаем данные дополнительной карты из настроек
        $cardData = $this->getPaymentSetting('manual_card_data');
        
        return [
            'payment_id' => $paymentId,
            'method' => 'manual_card',
            'instructions' => [
                'title' => '💳 Перевод на карту',
                'steps' => [
                    '1️⃣ Откройте банковское приложение',
                    '2️⃣ Переводы → На карту другого банка',
                    '3️⃣ Номер карты: ' . ($cardData['card_number'] ?? 'XXXX XXXX XXXX XXXX'),
                    '4️⃣ Сумма: ' . number_format($amount, 2) . ' ₽',
                    '5️⃣ Назначение: Пополнение #' . $paymentId,
                    '6️⃣ Подтвердите операцию'
                ],
                'card_number' => $cardData['card_number'] ?? 'Не указан',
                'holder_name' => $cardData['holder_name'] ?? 'Не указан',
                'bank_name' => $cardData['bank_name'] ?? 'Не указан'
            ]
        ];
    }
    
    public function checkPaymentStatus($paymentId) {
        $payment = $this->db->fetch(
            'SELECT * FROM payments WHERE id = :id',
            ['id' => $paymentId]
        );
        
        if (!$payment) {
            return false;
        }
        
        // Для ручных методов оплаты статус обновляется администратором
        return $payment['status'] === 'completed';
    }
    
    public function confirmPayment($paymentId, $adminId = null) {
        $payment = $this->db->fetch('SELECT * FROM payments WHERE id = :id', ['id' => $paymentId]);
        
        if (!$payment || $payment['status'] !== 'pending') {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            // Обновляем статус платежа
            $this->db->update('payments', [
                'status' => 'completed',
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = :id', ['id' => $paymentId]);
            
            // Если это пополнение баланса (deal_id = 0 или NULL)
            if (!$payment['deal_id']) {
                // Пополняем баланс пользователя
                require_once 'User.php';
                $user = new User($this->db);
                $user->updateBalance($payment['user_id'], $payment['amount'], 'add');
            } else {
                // Обновляем статус сделки на "оплачена"
                $this->db->update('deals', [
                    'status' => 'paid',
                    'payment_method' => $payment['payment_method'],
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'id = :id', ['id' => $payment['deal_id']]);
                
                // Добавляем системное сообщение к сделке
                require_once 'Deal.php';
                $deal = new Deal($this->db);
                $deal->addDealMessage($payment['deal_id'], 0, "Платеж подтвержден администратором", true);
            }
            
            // Логируем действие администратора
            if ($adminId) {
                $this->db->insert('admin_logs', [
                    'admin_id' => $adminId,
                    'action' => 'confirm_payment',
                    'target_type' => 'payment',
                    'target_id' => $paymentId,
                    'details' => "Подтвержден платеж на сумму {$payment['amount']} руб.",
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    public function rejectPayment($paymentId, $reason = '', $adminId = null) {
        $payment = $this->db->fetch('SELECT * FROM payments WHERE id = :id', ['id' => $paymentId]);
        
        if (!$payment || $payment['status'] !== 'pending') {
            return false;
        }
        
        $this->db->update('payments', [
            'status' => 'failed',
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $paymentId]);
        
        // Логируем действие администратора
        if ($adminId) {
            $this->db->insert('admin_logs', [
                'admin_id' => $adminId,
                'action' => 'reject_payment',
                'target_type' => 'payment',
                'target_id' => $paymentId,
                'details' => "Отклонен платеж на сумму {$payment['amount']} руб. Причина: {$reason}",
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        return true;
    }
    
    public function getPaymentMethods() {
        return [
            'sberbank' => [
                'name' => 'Сбербанк',
                'icon' => '🟢',
                'description' => 'Перевод через Сбербанк Онлайн',
                'enabled' => true
            ],
            'mir_card' => [
                'name' => 'Карта МИР',
                'icon' => '💳',
                'description' => 'Перевод на карту МИР',
                'enabled' => true
            ],
            'manual_card' => [
                'name' => 'Банковская карта',
                'icon' => '💰',
                'description' => 'Перевод на банковскую карту',
                'enabled' => true
            ]
        ];
    }
    
    private function getPaymentSetting($key) {
        $setting = $this->db->fetch(
            'SELECT setting_value FROM bot_settings WHERE setting_key = :key',
            ['key' => $key]
        );
        
        return $setting ? json_decode($setting['setting_value'], true) : [];
    }
    
    public function updatePaymentSetting($key, $data) {
        $existingSetting = $this->db->fetch(
            'SELECT id FROM bot_settings WHERE setting_key = :key',
            ['key' => $key]
        );
        
        if ($existingSetting) {
            $this->db->update('bot_settings', [
                'setting_value' => json_encode($data),
                'updated_at' => date('Y-m-d H:i:s')
            ], 'setting_key = :key', ['key' => $key]);
        } else {
            $this->db->insert('bot_settings', [
                'setting_key' => $key,
                'setting_value' => json_encode($data),
                'description' => 'Настройки платежной системы',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    public function getPendingPayments() {
        return $this->db->fetchAll('
            SELECT p.*, 
                   u.first_name, u.last_name, u.username,
                   d.deal_number, d.title as deal_title
            FROM payments p
            LEFT JOIN users u ON p.user_id = u.telegram_id
            LEFT JOIN deals d ON p.deal_id = d.id
            WHERE p.status = "pending"
            ORDER BY p.created_at DESC
        ');
    }
}