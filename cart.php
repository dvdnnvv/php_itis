<?php

require_once 'Product.php';
require_once 'CartClass.php';

session_start();

$cart = $_SESSION['cart'] ?? new Cart();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Корзина</title>
</head>
<body>
    <h1>Корзина</h1>

    <?php if (empty($cart->getItems())): ?>
        <p>Корзина пуста</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Товар</th>
                <th>Цена</th>
                <th>Количество</th>
                <th>Сумма</th>
                <th></th>
            </tr>

            <?php foreach ($cart->getItems() as $id => $item): ?>
                <?php $product = $item['product']; ?>
                <?php $quantity = $item['quantity']; ?>
                <?php $sum = $product->getPrice() * $quantity; ?>

                <tr>
                    <td><?= htmlspecialchars($product->getTitle()) ?></td>
                    <td><?= number_format($product->getPrice(), 0, ',', ' ') ?> руб.</td>
                    <td><?= $quantity ?></td>
                    <td><?= number_format($sum, 0, ',', ' ') ?> руб.</td>
                    <td><a href="remove.php?id=<?= $id ?>">Удалить</a></td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td colspan="3" align="right"><strong>Итого:</strong></td>
                <td colspan="2"><strong><?= number_format($cart->getTotal(), 0, ',', ' ') ?> руб.</strong></td>
            </tr>
        </table>

        <p>
            <a href="clear.php">Очистить корзину</a>
        </p>
    <?php endif; ?>

    <p><a href="index.php">Продолжить покупки</a></p>
</body>
</html>