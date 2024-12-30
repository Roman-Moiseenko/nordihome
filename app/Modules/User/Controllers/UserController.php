<?php

namespace App\Modules\User\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\User\Entity\User;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Service\RegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use function redirect;
use function view;

class UserController extends Controller
{
    private RegisterService $service;
    private UserRepository $repository;

    public function __construct(RegisterService $service, UserRepository $repository)
    {
        $this->middleware(['auth:admin']);
        $this->middleware(['can:user'])->except(['search_add']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $users = $this->repository->getIndex($request, $filters);
        $type_pricing = array_select(User::TYPE_PRICING);

        return Inertia::render('User/User/Index', [
            'users' => $users,
            'filters' => $filters,
            'type_pricing' => $type_pricing,
        ]);
    }

    public function create(Request $request)
    {
        try {
            $user = $this->service->create($request);
            return \response()->json($user->id);
        } catch (\DomainException $e) {
            return \response()->json(['error' => $e->getMessage()]);
        }

    }

    public function show(Request $request, User $user): Response
    {
        $type_pricing = array_select(User::TYPE_PRICING);
       // $organizations = Organization::orderBy('short_name')->active()->getModels();
        return Inertia::render('User/User/Show', [
            'user' => $this->repository->UserWithToArray($user, $request),
            //'organizations' => $organizations,
            'deliveries' => array_select(OrderExpense::DELIVERIES),
            'type_pricing' => $type_pricing,
        ]);
    }

    public function verify(User $user): RedirectResponse
    {
        try {
            $this->service->verifyAdmin($user->id);
            return redirect()->back()->with('success', 'Пользователь активирован');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function attach(Request $request, User $user): RedirectResponse
    {
        try {
            $this->service->attach($user, $request->integer('organization'));
            return redirect()->back()->with('success', 'Организация добавлена к клиенту');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function detach(Request $request, User $user): RedirectResponse
    {
        try {
            $this->service->detach($user, $request->integer('organization'));
            return redirect()->back()->with('success', 'Организация отсоединена от клиента');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function default(Request $request, User $user): RedirectResponse
    {
        try {
            $this->service->default($user, $request->integer('organization'));
            return redirect()->back()->with('success', 'Организация выбрана по умолчанию');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function set_info(Request $request, User $user): RedirectResponse
    {
        try {
            $this->service->setInfo($user, $request);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function upload(User $user, Request $request): RedirectResponse
    {
        try {
            $this->service->upload($user, $request);
            return redirect()->back()->with('success', 'Файл загружен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function search_add(Request $request): JsonResponse
    {
        $users = $this->repository->search($request->string('search')->trim()->value());
        return \response()->json($users);
    }
}
