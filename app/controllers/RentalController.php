<?php

namespace App\Controllers;

use App\Core\Controller;

class RentalController extends Controller
{
    public function create(): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция аренды в разработке']);
    }

    public function show(string $id): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция аренды в разработке']);
    }
}