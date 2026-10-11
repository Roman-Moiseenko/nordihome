<?php


namespace App\Modules\Cabinet\Presentation\Http\Controllers;


use App\Modules\Cabinet\Application\Queries\PageOptionsQuery;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopAbstractController;
use Illuminate\Http\Request;
use function view;

class OptionsController extends ShopAbstractController
{



    public function __construct(

        private readonly PageOptionsQuery $pageOptionsQuery,
    )
    {

    }

    public function index(Request $request)
    {
        $data = $this->pageOptionsQuery->execute($this->getClient($request));


        return view('cabinet.options', ['pageData' => $data]);
    }

    //AJAX
    public function subscription(mixed $subscription): \Illuminate\Http\JsonResponse
    {
        abort(404);
      // $this->service->toggle($user, $subscription);
       // return response()->json(true);
    }
}
