<?php

require_once 'CartClass.php';

session_start();

if (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
    $cart->clear();
    $_SESSION['cart'] = $cart;
}

header('Location: cart.php');