<?php

require_once 'Product.php';

class Cart {
    private array $items = [];

    public function add(Product $product, int $quantity = 1): void {
        $id = $product->getId();

        if (isset($this->items[$id])) {
            $this->items[$id]['quantity'] += $quantity;
        } else {
            $this->items[$id] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }
    }

    public function remove(int $productId): void {
        if (isset($this->items[$productId])) {
            unset($this->items[$productId]);
        }
    }

    public function getItems(): array {
        return $this->items;
    }

    public function getTotal(): float {
        $total = 0;

        foreach ($this->items as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        return $total;
    }

    public function clear(): void {
        $this->items = [];
    }
}