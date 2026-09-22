<?php
declare(strict_types=1);

namespace App\Modules\Cabinet\Presentation\Http\Controllers;

use App\Modules\Cabinet\Application\Queries\GetReviewsClientQuery;
use App\Modules\Cabinet\Application\Queries\PageReviewQuery;
use App\Modules\Catalog\Entity\Review;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopAbstractController;
use Illuminate\Http\Request;

class ReviewAbstractController extends ShopAbstractController
{
    public function __construct(
        public PageReviewQuery $pageReviewQuery
    )
    {
    }
    public function index(Request $request)
    {
        $client = $this->getClient($request);

        $data = $this->pageReviewQuery->execute($client);
        return view('cabinet.review.index', ['pageData' => $data]);
    }

    public function show(int $id, Request $request)
    {
        //TODO View UseCase
        $review = Review::find($id);
        return view('cabinet.review.show', compact('review'));
    }
}
