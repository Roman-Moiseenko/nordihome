<?php
declare(strict_types=1);

namespace App\Modules\User;

use App\Modules\User\Entity\User;
use App\Modules\User\Entity\Wish;

class UserRepository
{
    public function getWish(User $user): array
    {
        return array_map(function (Wish $wish) {
            return [
                'img' => $wish->product->photo->getThumbUrl('thumb'),
                'name' => $wish->product->name,
                'url' => route('shop.product.view', $wish->product),
                'cost' => $wish->product->getLastPrice(),
                'remove' => route('cabinet.wish.toggle', $wish->product),
                'product_id' => $wish->product->id,
            ];
        }, $user->wishes()->getModels());
    }
}
