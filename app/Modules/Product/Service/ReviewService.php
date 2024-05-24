<?php


namespace App\Modules\Product\Service;


use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Review;
use App\Modules\User\Entity\User;

class ReviewService
{

    public function generateUrl(Product $product, User $user): string
    {
        //Создание пустого отзыва не активного
        if (empty($review = Review::where('product_id', $product->id)->where('user_id', $user->id)->first())) {
            $review = Review::empty($product->id, $user->id);
        }
        //TODO Генерация ссылки
        return route('shop.product.review.show', $review);


    }
}
