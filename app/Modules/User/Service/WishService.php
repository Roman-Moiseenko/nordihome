<?php
declare(strict_types=1);

namespace App\Modules\User\Service;

use App\Modules\User\Entity\Wish;

class WishService
{

    public function toggle(int $user_id, int $product_id): bool
    {
        $wish = Wish::where('user_id', $user_id)->where('product_id', $product_id)->first();
        if (empty($wish)) {
            Wish::register($user_id, $product_id);
            return true;
        } else {
            $wish->delete();
            return false;
        }
    }
}
