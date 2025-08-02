<?php

class PaymentSystem {
    private $db;
    private $config;
    
    public function __construct($db, $config) {
        $this->db = $db;
        $this->config = $config;
    }
    
    public function createPayment($dealId, $userId, $amount, $method = 'yoomoney') {
        // Создаем запись о платеже
        $paymentId = $this->db->insert('payments', [
            'deal_id' => $dealId,
            'user_id' => $userId,
            'amount' => $amount,
            'payment_method' => $method,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Генерируем ссылку на оплату в зависимости от метода
        switch ($method) {
            case 'yoomoney':
                return $this->createYooMoneyPayment($paymentId, $amount);
            case 'qiwi':
                return $this->createQiwiPayment($paymentId, $amount);
            default:
                throw new Exception('Неподдерживаемый метод оплаты');
        }
    }
    
    private function createYooMoneyPayment($paymentId, $amount) {
        $params = [
            'receiver' => $this->config['payments']['yoomoney_wallet'],
            'quickpay-form' => 'shop',
            'targets' => "Оплата сделки #{$paymentId}",
            'paymentType' => 'SB',
            'sum' => $amount,
            'label' => $paymentId
        ];
        
        $url = 'https://yoomoney.ru/quickpay/confirm.xml?' . http_build_query($params);
        
        return [
            'payment_id' => $paymentId,
            'payment_url' => $url,
            'method' => 'yoomoney'
        ];
    }
    
    private function createQiwiPayment($paymentId, $amount) {
        // Интеграция с QIWI API
        $billId = 'bill_' . $paymentId . '_' . time();
        
        $data = [
            'amount' => [
                'currency' => 'RUB',
                'value' => number_format($amount, 2, '.', '')
            ],
            'comment' => "Оплата сделки #{$paymentId}",
            'expirationDateTime' => date('c', strtotime('+1 hour')),
            'customer' => [],
            'customFields' => [
                'paySourcesFilter' => 'qw,card'
            ]
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->config['payments']['qiwi_token'],
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.qiwi.com/partner/bill/v1/bills/{$billId}");
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            
            // Сохраняем ID счета в базе
            $this->db->update('payments', [
                'payment_id' => $billId
            ], 'id = :id', ['id' => $paymentId]);
            
            return [
                'payment_id' => $paymentId,
                'payment_url' => $result['payUrl'],
                'method' => 'qiwi',
                'bill_id' => $billId
            ];
        } else {
            throw new Exception('Ошибка создания счета QIWI');
        }
    }
    
    public function checkPaymentStatus($paymentId) {
        $payment = $this->db->fetch(
            'SELECT * FROM payments WHERE id = :id',
            ['id' => $paymentId]
        );
        
        if (!$payment) {
            return false;
        }
        
        switch ($payment['payment_method']) {
            case 'yoomoney':
                return $this->checkYooMoneyPayment($payment);
            case 'qiwi':
                return $this->checkQiwiPayment($payment);
            default:
                return false;
        }
    }
    
    private function checkYooMoneyPayment($payment) {
        // Для YooMoney проверка через webhook или API истории операций
        // Здесь упрощенная версия - в реальности нужно использовать API
        return $payment['status'] === 'completed';
    }
    
    private function checkQiwiPayment($payment) {
        if (!$payment['payment_id']) {
            return false;
        }
        
        $headers = [
            'Authorization: Bearer ' . $this->config['payments']['qiwi_token'],
            'Accept: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.qiwi.com/partner/bill/v1/bills/{$payment['payment_id']}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            
            if ($result['status']['value'] === 'PAID') {
                $this->updatePaymentStatus($payment['id'], 'completed');
                return true;
            }
        }
        
        return false;
    }
    
    public function updatePaymentStatus($paymentId, $status) {
        $this->db->update('payments', [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $paymentId]);
        
        // Если платеж завершен, обновляем статус сделки
        if ($status === 'completed') {
            $payment = $this->db->fetch('SELECT * FROM payments WHERE id = :id', ['id' => $paymentId]);
            if ($payment) {
                // Обновляем статус сделки на "оплачена"
                $this->db->update('deals', [
                    'status' => 'paid',
                    'payment_method' => $payment['payment_method'],
                    'payment_id' => $payment['payment_id']
                ], 'id = :id', ['id' => $payment['deal_id']]);
                
                // Добавляем системное сообщение
                require_once 'Deal.php';
                $deal = new Deal($this->db);
                $deal->addDealMessage($payment['deal_id'], 0, "Платеж успешно обработан", true);
            }
        }
    }
    
    public function refundPayment($paymentId, $reason = '') {
        $payment = $this->db->fetch('SELECT * FROM payments WHERE id = :id', ['id' => $paymentId]);
        
        if (!$payment || $payment['status'] !== 'completed') {
            return false;
        }
        
        // Логика возврата средств (зависит от платежной системы)
        switch ($payment['payment_method']) {
            case 'yoomoney':
                // Для YooMoney возврат через API
                $refunded = $this->refundYooMoneyPayment($payment, $reason);
                break;
            case 'qiwi':
                // Для QIWI возврат через API
                $refunded = $this->refundQiwiPayment($payment, $reason);
                break;
            default:
                $refunded = false;
        }
        
        if ($refunded) {
            $this->updatePaymentStatus($paymentId, 'refunded');
            
            // Возвращаем средства на баланс пользователя
            require_once 'User.php';
            $user = new User($this->db);
            $user->updateBalance($payment['user_id'], $payment['amount'], 'add');
        }
        
        return $refunded;
    }
    
    private function refundYooMoneyPayment($payment, $reason) {
        // Упрощенная версия - в реальности нужно использовать API возвратов
        return true;
    }
    
    private function refundQiwiPayment($payment, $reason) {
        // Упрощенная версия - в реальности нужно использовать API возвратов
        return true;
    }
    
    public function getPaymentMethods() {
        return [
            'yoomoney' => [
                'name' => 'ЮMoney',
                'icon' => '💳',
                'enabled' => !empty($this->config['payments']['yoomoney_token'])
            ],
            'qiwi' => [
                'name' => 'QIWI',
                'icon' => '🥝',
                'enabled' => !empty($this->config['payments']['qiwi_token'])
            ]
        ];
    }
    
    public function processWebhook($method, $data) {
        switch ($method) {
            case 'yoomoney':
                return $this->processYooMoneyWebhook($data);
            case 'qiwi':
                return $this->processQiwiWebhook($data);
            default:
                return false;
        }
    }
    
    private function processYooMoneyWebhook($data) {
        // Обработка webhook от YooMoney
        if (isset($data['label']) && isset($data['withdraw_amount'])) {
            $paymentId = $data['label'];
            $amount = $data['withdraw_amount'];
            
            // Проверяем существование платежа
            $payment = $this->db->fetch('SELECT * FROM payments WHERE id = :id', ['id' => $paymentId]);
            
            if ($payment && $payment['amount'] == $amount) {
                $this->updatePaymentStatus($paymentId, 'completed');
                return true;
            }
        }
        
        return false;
    }
    
    private function processQiwiWebhook($data) {
        // Обработка webhook от QIWI
        if (isset($data['bill']['billId']) && isset($data['bill']['status']['value'])) {
            $billId = $data['bill']['billId'];
            $status = $data['bill']['status']['value'];
            
            $payment = $this->db->fetch(
                'SELECT * FROM payments WHERE payment_id = :bill_id',
                ['bill_id' => $billId]
            );
            
            if ($payment) {
                $newStatus = $status === 'PAID' ? 'completed' : 'failed';
                $this->updatePaymentStatus($payment['id'], $newStatus);
                return true;
            }
        }
        
        return false;
    }
}