<?php

namespace App\Controllers;

use App\Core\Controller;

class MessageController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        
        $this->view('messages/index', [
            'title' => 'Сообщения'
        ]);
    }

    public function chat(string $userId): void
    {
        $this->requireAuth();
        
        $this->view('messages/chat', [
            'title' => 'Чат',
            'user_id' => intval($userId)
        ]);
    }

    public function send(): void
    {
        $this->requireAuth();
        
        $this->json(['success' => true, 'message' => 'Сообщение отправлено']);
    }

    public function unread(): void
    {
        $this->requireAuth();
        
        $this->json(['count' => 0]);
    }
}