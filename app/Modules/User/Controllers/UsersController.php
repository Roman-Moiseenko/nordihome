<?php

namespace App\Modules\User\Controllers;


use App\Http\Controllers\Controller;
use App\Modules\User\Entity\User;
use App\Modules\User\Repository\UserRepository;
use App\Modules\User\Service\RegisterService;
use Illuminate\Http\Request;
use function redirect;
use function view;

class UsersController extends Controller
{
    private RegisterService $service;
    private UserRepository $repository;

    public function __construct(RegisterService $service, UserRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:user']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $statuses = [
            User::STATUS_WAIT => 'В Ожидании',
            User::STATUS_ACTIVE => 'Подтвержден',
        ];
        $users = $this->repository->getIndex($request, $filters);

        return view('admin.user.index', compact('users' ,'filters', 'statuses'));
    }

    public function show(User $user)
    {
        $all = 0;
        $completed = 0;
        $amount_all = 0;
        $amount_completed = 0;
        foreach ($user->orders as $order) {
            $all++;
            $amount_all += $order->getTotalAmount();
            if ($order->isCompleted()) {
                $completed++;
                $amount_completed += $order->getExpenseAmount();
            }
        }
        return view('admin.user.show', compact('user', 'all', 'completed', 'amount_all', 'amount_completed'));
    }


    public function verify(User $user)
    {
        $this->service->verify($user->id);
        return redirect()->route('admin.user.show', $user);
    }
}
