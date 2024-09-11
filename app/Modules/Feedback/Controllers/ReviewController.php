<?php
declare(strict_types=1);

namespace App\Modules\Feedback\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Review;
use App\Modules\Product\Repository\ReviewRepository;
use App\Modules\Product\Service\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    private ReviewService $service;
    private ReviewRepository $repository;

    public function __construct(ReviewService $service, ReviewRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $filter = $request['filter'] ?? 'moderated';
        $count_moderated = $this->repository->countModerated();
        $query = $this->repository->getIndex($filter);

        $reviews = $this->pagination($query, $request, $pagination);
        return view('admin.feedback.review.index', compact('reviews', 'filter', 'pagination', 'count_moderated'));
    }


    public function show(Review $review)
    {
        return view('admin.feedback.review.show', compact('review'));
    }

    public function published(Review $review)
    {
        $this->service->published($review);
        return redirect()->back();
    }

    public function blocked(Review $review)
    {
        $this->service->blocked($review);
        return redirect()->back();
    }
}
