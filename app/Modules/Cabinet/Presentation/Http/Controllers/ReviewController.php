<?php
declare(strict_types=1);

namespace App\Modules\Cabinet\Presentation\Http\Controllers;

use App\Modules\Catalog\Entity\Review;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;

class ReviewController extends ShopController
{

    public function index()
    {
        //FIXME - Отзывы сделать useCase для получения всех отзывово, через пагинацию
        // Либо отдельный CabinetReviewPageData
        $reviews = [];
        return view('shop.cabinet.review.index', ['reviews' => $reviews]);
    }

    public function show(Review $review)
    {
        return view('shop.cabinet.review.show', compact('review'));
    }
}
