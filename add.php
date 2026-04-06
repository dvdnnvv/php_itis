<?php

require_once 'Product.php';
require_once 'Cart.php';

$products = [
    1 => new Product(1, 'Ноутбук ASUS', 50000),
    2 => new Product(2, 'Мышь Logitech', 1500),
    3 => new Product(3, 'Клавиатура механическая', 3000),
    4 => new Product(4, 'Наушники Sony', 4000),
    5 => new Product(5, 'Монитор LG', 12000),
];

$id = (int)$_GET['id'];

if (!isset($products[$id])) {
    die('Товар не найден');
}

session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = new Cart();
}

$cart = $_SESSION['cart'];
$cart->add($products[$id], 1);
$_SESSION['cart'] = $cart;

header('Location: index.php');