<?php
declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\Product\Entity\Product;
use App\Modules\User\Entity\CartCookie;

class CartCookieService
{
    public function clearByUser(string $ui)
    {
        CartCookie::where('user_ui', $ui)->delete();
    }

    public function toStorage(string $user_ui, Product $product, int $quantity, array $options = [])
    {
        CartCookie::register(
            $user_ui,
            $product->id,
            $quantity,
            $options
        );
    }

    public function updateStorage(int $id, int $new_quantity)
    {
        $cookie = CartCookie::find($id);
        $cookie->update([
            'quantity' => $new_quantity,
        ]);
    }

    public function fromStorage(int $id)
    {
        CartCookie::destroy($id);
    }
}
