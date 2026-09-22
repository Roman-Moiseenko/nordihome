<?php


namespace App\Modules\Cabinet\Presentation\Http\Controllers;


use App\Modules\Cabinet\Application\Queries\PageOptionsQuery;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopAbstractController;
use App\Modules\User\Entity\Subscription;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function response;
use function view;

class OptionsAbstractController extends ShopAbstractController
{

    private SubscriptionService $service;

    public function __construct(
        SubscriptionService $service,
        private readonly PageOptionsQuery $pageOptionsQuery,
    )
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $data = $this->pageOptionsQuery->execute($this->getClient($request));


        return view('cabinet.options', ['pageData' => $data]);
    }

    //AJAX
    public function subscription(Subscription $subscription): \Illuminate\Http\JsonResponse
    {
        abort(404);
      // $this->service->toggle($user, $subscription);
       // return response()->json(true);
    }
}
