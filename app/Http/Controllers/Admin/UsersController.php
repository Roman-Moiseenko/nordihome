<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Modules\User\Entity\User;
use App\Modules\User\Service\RegisterService;
use App\Modules\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

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
        return $this->try_catch_admin(function () use($request) {
            $statuses = [
                User::STATUS_WAIT => 'В Ожидании',
                User::STATUS_ACTIVE => 'Подтвержден',
            ];
            $query = $this->repository->getIndex($request);
            $users = $this->pagination($query, $request, $pagination);

            return view('admin.users.index', compact('users', 'statuses', 'pagination'));
        });
    }

    public function show(User $user)
    {
        return $this->try_catch_admin(function () use($user) {
            return view('admin.users.show', compact('user'));
        });
    }

    public function verify(User $user)
    {
        return $this->try_catch_admin(function () use($user) {
            $this->service->verify($user->id);
            return redirect()->route('admin.users.show', $user);
        });
    }
}
