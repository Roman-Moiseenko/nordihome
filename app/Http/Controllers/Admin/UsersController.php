<?php

namespace App\Http\Controllers\Admin;

use App\Entity\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = [
            User::STATUS_WAIT => 'В Ожидании',
            User::STATUS_ACTIVE => 'Подтвержден',
        ];
        $roles = [];
        $users = User::orderBy('id', 'desc')->paginate(20);
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
        return view('admin.users.edit', compact('user', 'statuses'));

    }

    public function update(UpdateRequest $request, User $user)
    {

        $user->update($request->only(['name', 'email']));
        return view('admin.users.show', compact('user'));

    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index');
    }
    public function verify(User $user)
    {
        $user->verify();
        return redirect()->route('admin.users.show', $user);
    }
}
