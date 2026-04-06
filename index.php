<?php

require_once 'Product.php';

$products = [
    new Product(1, 'Ноутбук ASUS', 50000),
    new Product(2, 'Мышь Logitech', 1500),
    new Product(3, 'Клавиатура механическая', 3000),
    new Product(4, 'Наушники Sony', 4000),
    new Product(5, 'Монитор LG', 12000),
];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Магазин</title>
</head>
<body>
    <h1>Товары</h1>

    <ul>
        <?php foreach ($products as $product): ?>
            <li>
                <?= htmlspecialchars($product->getTitle()) ?> -
                <?= number_format($product->getPrice(), 0, ',', ' ') ?> руб.
                <a href="add.php?id=<?= $product->getId() ?>">Добавить в корзину</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <p><a href="cart.php">Перейти в корзину</a></p>
</body>
</html>