<?php

require_once 'CartClass.php';

$id = (int)$_GET['id'];

session_start();

if (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
    $cart->remove($id);
    $_SESSION['cart'] = $cart;
}

header('Location: cart.php');