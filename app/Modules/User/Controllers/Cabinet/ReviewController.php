<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Entity\Review;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use Illuminate\Support\Facades\Auth;

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
