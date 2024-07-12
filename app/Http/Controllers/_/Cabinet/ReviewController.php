<?php
declare(strict_types=1);

namespace App\Http\Controllers\_\Cabinet;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Review;
use function view;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:user']);
    }

    public function index()
    {
        return $this->try_catch(function () {

            return view('cabinet.review.index');
        });
    }

    public function show(Review $review)
    {
        return $this->try_catch(function () use ($review) {

            return view('cabinet.review.show', compact('review'));
        });
    }
}
