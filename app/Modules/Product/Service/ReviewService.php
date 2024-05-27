<?php


namespace App\Modules\Product\Service;


use App\Events\CouponHasCreated;
use App\Events\ReviewHasEdit;
use App\Modules\Admin\Entity\Options;
use App\Modules\Discount\Entity\Coupon;
use App\Modules\Discount\Entity\DiscountReview;
use App\Modules\Order\Entity\Order\Order;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Review;
use App\Modules\User\Entity\User;
use Illuminate\Support\Facades\Auth;

class ReviewService
{
    private bool $bonus_review;
    private float $bonus_amount;

    public function __construct()
    {
        $this->bonus_amount = (new Options())->shop->bonus_amount;
        $this->bonus_review = (new Options())->shop->bonus_review;;
    }

    public function createEmpty(Product $product, User $user, Order $order): Review
    {
        //Создание пустого отзыва не активного
        if (empty($review = Review::where('product_id', $product->id)->where('user_id', $user->id)->first())) {
            $review = Review::empty($product->id, $user->id);
        }

        if ($this->bonus_review && is_null($review->discount)) {
            $review->discount()->save(DiscountReview::new($this->bonus_amount, $order->id)); //Создаем бонус для данного отзыва
        }

        return $review;
    }

    public function create(array $request): Review
    {
        $user = Auth::guard('user')->user();
        if (is_null($user)) throw new \DomainException('Отзыв можно оставлять только зарегистрированным пользователям!');
        $review = Review::register((int)$request['product_id'], $user->id, $request['text'],(int)$request['rating'] );

        event(new ReviewHasEdit($review));
        return $review;
    }

    public function update(Review $review, string $text, int $rating): Review
    {
        if ($review->status == Review::STATUS_BLOCKED) throw new \DomainException('Отзыв заблокирован, менять нельзя');
        if ($review->text == $text && $review->rating = $rating) return $review; //Данные не изменились

        $review->text = $text;
        $review->rating = $rating;


        if ($review->status == Review::STATUS_DRAFT) {
            $review->status = Review::STATUS_MODERATED;
            $review->save();
            $review->refresh();
            $this->check_discount_review($review);
        }

        if ($review->status == Review::STATUS_PUBLISHED) {
            $review->status = Review::STATUS_MODERATED;
            $review->save();
            $review->refresh();
            $review->product->updateReview();
        }
        event(new ReviewHasEdit($review));
        return $review;
    }

    private function check_discount_review(Review $review)
    {
        $order_id = $review->discount->order_id;
        $user = $review->user;

        $amount = 0;

        foreach ($user->reviews as $item) {
            if (!is_null($item->discount) && $item->discount->order_id == $order_id && !$item->discount->isUsed()) {
                if ($item->status == Review::STATUS_DRAFT) return; //Если какой отзыв из заказа еще не опубликован
                $amount += $item->discount->amount;
            }
        }

        if ($amount > 0) {
            //Отмечаем все скидки как использованные
            foreach ($user->reviews as $item) {
                if (!is_null($item->discount) && $item->discount->order_id == $order_id && !$item->discount->isUsed()) {
                    $item->discount->used();
                }
            }

            $coupon = Coupon::register($user->id, $amount, now(), now()->addYear());
            event(new CouponHasCreated($coupon));
        }
    }

    public function published(Review $review)
    {
        $review->status = Review::STATUS_PUBLISHED;
        $review->save();
        $review->refresh();

        //Пересчет рейтинга товара
        $review->product->updateReview();
        //TODO Сообщаем? клиенту
    }

    public function blocked(Review $review)
    {
        $review->status = Review::STATUS_BLOCKED;
        $review->save();
        //TODO Сообщаем? клиенту
    }


}
