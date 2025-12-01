<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Modules\Product\Entity\Review;
use Illuminate\Support\Facades\Auth;

class ReviewController extends AuthCabinetController
{

    public function index()
    {
        return view($this->route('cabinet.review.index'));
    }

    public function show(Review $review)
    {
        return view($this->route('cabinet.review.show'), compact('review'));
    }
}
