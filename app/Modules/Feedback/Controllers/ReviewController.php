<?php
declare(strict_types=1);

namespace App\Modules\Feedback\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Review;
use App\Modules\Product\Service\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    private ReviewService $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $filter = $request['filter'] ?? 'moderated';
            $count_moderated = Review::where('status', Review::STATUS_MODERATED)->count();
            $query = Review::orderByDesc('created_at'); //$this->repository->getIndex($request);
            if ($filter == 'moderated') $query->where('status', Review::STATUS_MODERATED);
            if ($filter == 'draft') $query->where('status', Review::STATUS_DRAFT);
            if ($filter == 'published') $query->where('status', Review::STATUS_PUBLISHED);
            if ($filter == 'blocked') $query->where('status', Review::STATUS_BLOCKED);
            $reviews = $this->pagination($query, $request, $pagination);
            return view('admin.feedback.review.index', compact('reviews', 'filter', 'pagination', 'count_moderated'));
        });
    }


    public function show(Review $review)
    {
        return view('admin.feedback.review.show', compact('review'));
    }

    public function published(Review $review)
    {
        return $this->try_catch_admin(function () use($review) {
            $this->service->published($review);
            return redirect()->back();
        });
    }

    public function blocked(Review $review)
    {
        return $this->try_catch_admin(function () use($review) {
            $this->service->blocked($review);
            return redirect()->back();
        });
    }
}
