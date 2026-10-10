<?php
declare(strict_types=1);

namespace App\Modules\Feedback\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Infrastructure\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {
      /*  $filter = $request['filter'] ?? 'moderated';
        $count_moderated = $this->repository->countModerated();
        $query = $this->repository->getIndex($filter);

        $reviews = $this->pagination($query, $request, $pagination);
        */
        //TODO страница на Inertia
       // return view('admin.feedback.review.index', compact('reviews', 'filter', 'pagination', 'count_moderated'));
    }


    public function show(Review $review)
    {
        //TODO страница на Inertia
      //  return view('admin.feedback.review.show', compact('review'));
    }

    public function published(Review $review)
    {
  //      $this->service->published($review);
        return redirect()->back();
    }

    public function blocked(Review $review)
    {
     //   $this->service->blocked($review);
        return redirect()->back();
    }
}
