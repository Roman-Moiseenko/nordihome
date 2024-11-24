<?php
declare(strict_types=1);

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Entity\Subscription;
use App\Modules\User\Repository\SubscriptionRepository;
use App\Modules\User\Service\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use function redirect;
use function view;

class SubscriptionController extends Controller
{
    private SubscriptionService $service;
    private SubscriptionRepository $repository;

    public function __construct(SubscriptionService $service, SubscriptionRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $subscriptions = $this->repository->getIndex($request);
        return Inertia::render('User/Subscription/Index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    public function show(Subscription $subscription): Response
    {
        return Inertia::render('User/Subscription/Show', [
            'subscription' => $this->repository->SubscriptionWithToArray($subscription),
        ]);
    }


    public function activated(Subscription $subscription): RedirectResponse
    {
        try {
            $subscription->setActivated();
            return redirect()->back()->with('success', 'Подписка активирована');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function draft(Subscription $subscription): RedirectResponse
    {
        try {
            $subscription->setDraft();
            $subscription->users()->detach();
            return redirect()->back()->with('success', 'Подписка отменена, пользователи отключены.');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(Request $request, Subscription $subscription): RedirectResponse
    {
        try {
            $this->service->setInfo($subscription, $request);

            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
