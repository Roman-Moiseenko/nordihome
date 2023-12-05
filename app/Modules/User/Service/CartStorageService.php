<?php
declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\CartStorage;

class CartStorageService
{
    public function clearByUser($id)
    {
        CartStorage::where('user_id', $id)->delete();
    }

    public function toStorage(int $user_id, Product $product, int $quantity, ?int $reserve_id, array $options = [])
    {
        CartStorage::register(
            $user_id,
            $product->id,
            $quantity,
            $reserve_id,
            $options
        );
    }

    public function updateStorage(int $id, int $new_quantity)
    {
        $cookie = CartStorage::find($id);
        $cookie->update([
            'quantity' => $new_quantity,
        ]);
    }

    public function fromStorage(int $id)
    {
        CartStorage::destroy($id);
    }
}
