<?php

namespace App\Http\Controllers\Admin;

use App\Entity\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\UseCases\Auth\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    private RegisterService $service;

    /**
     * Display a listing of the resource.
     */

    public function __construct(RegisterService $service)
    {
        //$this->middleware('can:admin');
        $this->middleware('can:user-manager');
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $statuses = [
            User::STATUS_WAIT => 'В Ожидании',
            User::STATUS_ACTIVE => 'Подтвержден',
        ];
        $roles = User::ROLES;
        $query = User::orderByDesc('id');
        if (!empty($value = $request->get('id'))) {
            $query->where('id', $value);
        }

        if (!empty($value = $request->get('name'))) {
            $query->where('name', 'like', '%' . $value . '%');
        }

        if (!empty($value = $request->get('email'))) {
            $query->where('email', 'like', '%' . $value . '%');
        }

        if (!empty($value = $request->get('status'))) {
            $query->where('status', $value);
        }

        if (!empty($value = $request->get('role'))) {
            $query->where('role', $value);
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users', 'statuses', 'roles'));
    }


    public function create()
    {
        return view('admin.users.create');
    }

    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt(Str::random()),
            'status' => User::STATUS_ACTIVE
        ]);

        return redirect()->route('admin.users.show', $user);
    }

    public function show(User $user)
    {
        //$user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $statuses = [
            User::STATUS_WAIT => 'В Ожидании',
            User::STATUS_ACTIVE => 'Подтвержден',
        ];
        $roles = User::ROLES;
        return view('admin.users.edit', compact('user', 'statuses', 'roles'));

    }

    public function update(UpdateRequest $request, User $user)
    {

        $user->update($request->only(['name', 'email']));
       if ($request['role'] !== $user->role) {
            $user->changeRole($request['role']);
        }
        return view('admin.users.show', compact('user'));

    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index');
    }
    public function verify(User $user)
    {
        $this->service->verify($user->id);
        return redirect()->route('admin.users.show', $user);
    }
}
