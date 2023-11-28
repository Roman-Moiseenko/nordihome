<?php

namespace App\Http\Controllers\Admin;

use App\Entity\User\User;
use App\Http\Controllers\Controller;
use App\Modules\User\Service\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UsersController extends Controller
{
    private RegisterService $service;
    private mixed $pagination;

    public function __construct(RegisterService $service)
    {
        $this->middleware('auth:admin');
        $this->middleware('can:user-manager');
        $this->service = $service;
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        $statuses = [
            User::STATUS_WAIT => 'В Ожидании',
            User::STATUS_ACTIVE => 'Подтвержден',
        ];
        //TODO Вынести в Репозиторий, после переноса User в модуль User\
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

        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $users = $query->paginate($pagination);
            $users->appends(['p' => $pagination]);
        } else {
            $users = $query->paginate($this->pagination);
        }
        return view('admin.users.index', compact('users', 'statuses', 'pagination'));
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
