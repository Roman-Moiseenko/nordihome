<?php
declare(strict_types=1);

namespace App\Modules\Shop\Cart\Storage;

interface StorageInterface
{
    public function load(): array;
    public function save(array $items): void;
}
