<?php


namespace App\Modules\User\Controllers\Cabinet;


use App\Http\Controllers\Controller;
use App\Modules\User\Entity\Subscription;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function response;
use function view;

class OptionsController extends Controller
{

    private SubscriptionService $service;

    public function __construct(SubscriptionService $service)
    {
        $this->middleware('auth:user');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $subscriptions = Subscription::where('published', true)->get();
        return view('cabinet.options', compact('subscriptions'));
    }

    //AJAX
    public function subscription(Subscription $subscription)
    {

        return $this->try_catch_ajax(function () use ($subscription) {
            /** @var User $user */
            $user = Auth::guard('user')->user();
            $this->service->toggle($user, $subscription);
            return response()->json(true);
        });
    }
}
