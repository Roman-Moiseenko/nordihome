<?php
declare(strict_types=1);

namespace App\Modules\Cart\Domain\Interfaces;

use App\Modules\Cart\Domain\Entities\CartItem;

interface StorageInterface
{
    public function load(): array;

    public function add(CartItem $item): void;
    public function sub(CartItem $item, float $quantity): void;
    public function plus(CartItem $item, float $quantity): void;

    public function remove(int $itemId): void;

    public function check(CartItem $item): void;

    public function clear(): void;

}
