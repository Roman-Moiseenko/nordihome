<?php

namespace App\Http\Controllers\Admin;

use App\Entity\User\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\UseCases\Auth\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    private RegisterService $service;

    public function __construct(RegisterService $service)
    {
        $this->middleware('auth:admin');
        $this->middleware('can:user-manager');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $statuses = [
            User::STATUS_WAIT => 'В Ожидании',
            User::STATUS_ACTIVE => 'Подтвержден',
        ];
        $query = User::orderByDesc('id');
        if (!empty($value = $request->get('id'))) {
            $query->where('id', $value);
        }
        if (!empty($value = $request->get('phone'))) {
            $query->where('phone', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('email'))) {
            $query->where('email', 'like', '%' . $value . '%');
        }
        if (!empty($value = $request->get('status'))) {
            $query->where('status', $value);
        }
        $users = $query->paginate(20);

        return view('admin.users.index', compact('users', 'statuses'/*, 'roles'*/));
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function verify(User $user)
    {
        $this->service->verify($user->id);
        return redirect()->route('admin.users.show', $user);
    }
}
