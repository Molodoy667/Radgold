<?php

namespace App\Controllers;

use App\Core\Controller;

class PageController extends Controller
{
    public function about(): void
    {
        $this->view('pages/about', [
            'title' => 'О нас'
        ]);
    }

    public function contact(): void
    {
        $this->view('pages/contact', [
            'title' => 'Контакты'
        ]);
    }

    public function terms(): void
    {
        $this->view('pages/terms', [
            'title' => 'Условия использования'
        ]);
    }

    public function privacy(): void
    {
        $this->view('pages/privacy', [
            'title' => 'Политика конфиденциальности'
        ]);
    }

    public function help(): void
    {
        $this->view('pages/help', [
            'title' => 'Помощь'
        ]);
    }
}