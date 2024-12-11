<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

use App\Modules\Shop\Cart\CartItem;

interface StorageInterface
{
    public function load(): array;

    public function add(CartItem $item): void;
    public function sub(CartItem $item, float $quantity): void;
    public function plus(CartItem $item, float $quantity): void;

    public function remove(CartItem $item): void;

    public function check(CartItem $item): void;

    public function clear(): void;

}
