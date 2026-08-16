<?php
declare(strict_types=1);

namespace App\Modules\Cabinet\Presentation\Http\Controllers;

use App\Modules\Cabinet\Application\Queries\GetReviewsClientQuery;
use App\Modules\Catalog\Entity\Review;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use Illuminate\Http\Request;

class ReviewController extends ShopController
{
    public function __construct(
        public GetReviewsClientQuery $getReviewsClientQuery,
    )
    {
    }
    public function index(Request $request)
    {
        //FIXME - Отзывы сделать useCase для получения всех отзывово, через пагинацию
        // Либо отдельный CabinetReviewPageData
        $client = $this->getClient($request);
        $reviews = $this->getReviewsClientQuery->execute($client->id);
        return view('cabinet.review.index', ['reviews' => $reviews]);
    }

    public function show(int $id, Request $request)
    {
        //TODO View UseCase
        $review = Review::find($id);
        return view('cabinet.review.show', compact('review'));
    }
}
