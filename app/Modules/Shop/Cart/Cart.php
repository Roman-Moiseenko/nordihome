<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart;

use App\Modules\Shop\Cart\Storage\StorageInterface;

class Cart
{
    /** @var CartItem[] $items */
    private array $items;

    private $storage;
    private $calculator;

    public function __construct(StorageInterface $storage)
    {
    }
}
