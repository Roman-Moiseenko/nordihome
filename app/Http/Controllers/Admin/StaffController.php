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
        $admins = $query->paginate(9);
        return view('admin.staff.index', compact('admins', 'roles'));
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
        $staff = Admin::register(
            $request['name'],
            $request['email'],
            $request['phone'],
            $request['password']
        );
        return redirect()->route('admin.staff.show', compact('staff'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $staff)
    {
        return view('admin.staff.show', compact('staff'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $staff)
    {
        $roles = Admin::ROLES;
        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $staff)
    {

        $staff->update($request->all());
        return view('admin.staff.show', compact('staff'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $staff)
    {
        //TODO Проверить на связанны данные, если есть вызвать исключение
        //
    }
}
