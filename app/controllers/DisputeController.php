<?php

namespace App\Controllers;

use App\Core\Controller;

class DisputeController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $this->view('disputes/index', ['title' => 'Споры']);
    }

    public function createForm(string $purchaseId): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция в разработке']);
    }

    public function create(): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция в разработке']);
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция в разработке']);
    }
}