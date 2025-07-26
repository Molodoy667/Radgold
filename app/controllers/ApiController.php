<?php

namespace App\Controllers;

use App\Core\Controller;

class ApiController extends Controller
{
    public function stats(): void
    {
        $this->json([
            'users' => 150,
            'products' => 300,
            'sales' => 45
        ]);
    }

    public function search(): void
    {
        $input = $this->getInput();
        $query = $input['q'] ?? '';
        
        $this->json([
            'query' => $query,
            'results' => []
        ]);
    }

    public function upload(): void
    {
        $this->requireAuth();
        $this->json(['success' => false, 'message' => 'Функция в разработке']);
    }
}