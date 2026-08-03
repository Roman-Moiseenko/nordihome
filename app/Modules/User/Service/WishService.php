<?php
declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\User\Entity\Wish;

class WishService
{

    public function toggle(int $client_id, int $product_id): bool
    {
        $wish = Wish::where('client_id', $client_id)->where('product_id', $product_id)->first();
        if (empty($wish)) {
            Wish::register($client_id, $product_id);
            return true;
        } else {
            $wish->delete();
            return false;
        }
    }

    public function clear(int $client_id)
    {
        Wish::where('client_id', $client_id)->delete();
    }
}
