<?php
namespace App\Controllers;

use App\AbstractController;

class HomeController extends AbstractController
{
    public function index()
    {
        echo "Главная страница салона красоты";
    }
}