<?php
declare(strict_types=1);

namespace App\Modules\Cart\Domain\Interfaces;

use App\Modules\Cart\Domain\Entities\CartItemEntity;

interface StorageInterface
{
    public function load(): array;

    public function add(CartItemEntity $item): void;
    public function sub(CartItemEntity $item, float $quantity): void;
    public function plus(CartItemEntity $item, float $quantity): void;

    public function remove(int $itemId): void;

    public function check(CartItemEntity $item): void;

    public function clear(): void;

}
