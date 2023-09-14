<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Admin;
use App\Http\Controllers\Controller;
use App\UseCases\Admin\RegisterService;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    private RegisterService $service;

    /**
     * Display a listing of the resource.
     */

    public function __construct(RegisterService $service)
    {
        $this->middleware(['auth:admin', 'can:user-manager']);
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $query = Admin::orderByDesc('id');
        if (!empty($value = $request->get('role'))) {
            $query->where('role', $value);
        }
        $roles = Admin::ROLES;
        $admins = $query->paginate(20);

        $layout = 'admin';
        return view('admin.staff.index', compact('admins', 'roles', 'layout'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.staff.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $admin = Admin::register(
            $request['name'],
            $request['email'],
            $request['phone'],
            $request['password']
        );
        return redirect()->route('admin.staff.show', $admin);
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        return view('admin.staff.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        $roles = Admin::ROLES;
        return view('admin.staff.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {

        $admin->update($request->all());
        return view('admin.staff.show', compact('admin'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //TODO Проверить на связанны данные, если есть вызвать исключение
        //
    }
}
