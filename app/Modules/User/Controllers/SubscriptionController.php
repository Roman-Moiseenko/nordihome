<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Entity\Subscription;
use App\Modules\User\Service\SubscriptionService;
use Illuminate\Http\Request;
use function redirect;
use function view;

class SubscriptionController extends Controller
{
    private SubscriptionService $service;

    public function __construct(SubscriptionService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $subscriptions = Subscription::orderBy('name')->get();
        return view('admin.user.subscription.index', compact('subscriptions'));
    }

    public function edit(Subscription $subscription)
    {
        return view('admin.user.subscription.edit', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $this->service->update($subscription, $request);
        return redirect()->route('admin.user.subscription.index');
    }

    public function published(Subscription $subscription)
    {
        $subscription->setPublished();
        return redirect()->back();
    }

    public function draft(Subscription $subscription)
    {
        $subscription->setDraft();
        $subscription->users()->detach();
        return redirect()->back();
    }

}
