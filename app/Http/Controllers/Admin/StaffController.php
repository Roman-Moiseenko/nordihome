<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Admin;
use App\Http\Controllers\Controller;
use App\UseCases\Admin\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $roles = Admin::ROLES;
        return view('admin.staff.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:admins|max:255',
            'email' => 'required|email|unique:admins',
            'phone' => 'required|numeric',
            'password' => 'required|string|min:6',
            'surname' => 'required|string|max:33',
            'firstname' => 'required|string|max:33',
            'secondname' => 'string|max:33',
            'post' => 'string|max:33',
            'role' => 'required|string|max:33',
            'photo' => '', //TODO Photo
        ]);
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
        flash('Пароль успешно изменен', 'success');
        return view('admin.staff.show', compact('staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $staff)
    {
        $request->validate([
            'name' => 'required|unique:admins,id,' . $staff->id,
            'email' => 'required|email|unique:admins,id,' . $staff->id,
            'phone' => 'required|numeric',
            'surname' => 'required|string|max:33',
            'firstname' => 'required|string|max:33',
            'secondname' => 'string|max:33',
            'post' => 'string|max:33',
            'role' => 'required|string|max:33',
            'photo' => '', //TODO Photo
        ]);
        $staff = $this->service->update($request, $staff);

        return view('admin.staff.show', compact('staff'));
    }

    /**
     * Remove the specified resource from storage.
     */
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

    //TODO Нужен ли action для Активации сотрудника ????
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



    public function test(Request $request)
    {
        return response()->json([
            'name' => $request['file'],
        ]);
    }
}
