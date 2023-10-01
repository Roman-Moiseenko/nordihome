<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest;
use App\Http\Requests\Admin\UpdateRequest;
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
        $selected = $request['role'] ?? '';
        $roles = Admin::ROLES;
        $admins = $query->paginate(9);
        return view('admin.staff.index', compact('admins', 'roles', 'selected'));
    }

    public function create()
    {
        $roles = Admin::ROLES;
        return view('admin.staff.create', compact('roles'));
    }

    public function store(RegisterRequest $request)
    {
        $staff = $this->service->register($request);
        return redirect()->route('admin.staff.show', compact('staff'));
    }

    public function show(Admin $staff)
    {
        return view('admin.staff.show', compact('staff'));
    }

    public function edit(Admin $staff)
    {
        $roles = Admin::ROLES;
        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    public function security(Admin $staff)
    {
        return view('admin.staff.security', compact('staff'));
    }

    public function password(Request $request, Admin $staff)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);
        try {
            $this->service->setPassword($request['password'], $staff);
            flash('Пароль успешно изменен', 'success');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return back();
    }

    public function update(UpdateRequest $request, Admin $staff)
    {
        $staff = $this->service->update($request, $staff);
        return view('admin.staff.show', compact('staff'));
    }

    public function destroy(Admin $staff)
    {
        try {
            $this->service->blocking($staff);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect('admin/staff');
    }

    public function activate(Admin $staff)
    {
        try {
            $this->service->activate( $staff);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect('admin/staff');
    }

    public function setPhoto(Request $request, Admin $staff)
    {
        try {
            $this->service->setPhoto($request->file('file'), $staff);
        } catch (\Throwable $e) {

            return response()->json([
                'error' => $e->getMessage()
            ]);
        }
        return response()->json([
            'name' => $staff->photo,
        ]);
    }

    public function test(Request $request)
    {
        return response()->json([
            'name' => $request['file'],
        ]);
    }
}
