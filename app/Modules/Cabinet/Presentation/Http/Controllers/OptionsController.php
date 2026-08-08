<?php


namespace App\Modules\Cabinet\Presentation\Http\Controllers;


use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use App\Modules\User\Entity\Subscription;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function response;
use function view;

class OptionsController extends ShopController
{

    private SubscriptionService $service;

    public function __construct(SubscriptionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $subscriptions = Subscription::orderBy('name')->active()->get();
        return view('shop.cabinet.options', compact('subscriptions'));
    }

    //AJAX
    public function subscription(Subscription $subscription): \Illuminate\Http\JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('web')->user();
        $this->service->toggle($user, $subscription);
        return response()->json(true);
    }
}
