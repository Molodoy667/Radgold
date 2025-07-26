<?php

namespace App\Controllers;

use App\Core\Controller;

class ReviewController extends Controller
{
    public function createForm(string $productId): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция в разработке']);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция в разработке']);
    }

    public function editForm(string $id): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция в разработке']);
    }

    public function update(string $id): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция в разработке']);
    }

    public function delete(string $id): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция в разработке']);
    }
}