<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest;
use App\Http\Requests\Admin\UpdateRequest;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Admin\Service\StaffService;
use Illuminate\Http\Request;


class StaffController extends Controller
{
    private StaffService $service;
    private StaffRepository $repository;

    /**
     * Display a listing of the resource.
     */

    public function __construct(StaffService $service, StaffRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:staff']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $selected = $request['role'] ?? '';
            $roles = Admin::ROLES;
            $query = $this->repository->getIndex($request);
            $admins = $this->pagination($query, $request, $pagination);
            return view('admin.staff.index', compact('admins', 'roles', 'selected', 'pagination'));
        });
    }

    public function create()
    {
        return $this->try_catch_admin(function () {
            $roles = Admin::ROLES;
            return view('admin.staff.create', compact('roles'));
        });
    }

    public function store(RegisterRequest $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $staff = $this->service->register($request);
            return redirect()->route('admin.staff.show', compact('staff'));
        });
    }

    public function show(Admin $staff)
    {
        return $this->try_catch_admin(function () use($staff) {
            return view('admin.staff.show', compact('staff'));
        });
    }

    public function edit(Admin $staff)
    {
        return $this->try_catch_admin(function () use($staff) {
            $roles = Admin::ROLES;
            return view('admin.staff.edit', compact('staff', 'roles'));
        });
    }

    public function security(Admin $staff)
    {
        return $this->try_catch_admin(function () use($staff) {
            return view('admin.staff.security', compact('staff'));
        });
    }

    public function password(Request $request, Admin $staff)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);
        return $this->try_catch_admin(function () use($request, $staff) {
            $this->service->setPassword($request['password'], $staff);
            flash('Пароль успешно изменен', 'success');
            return redirect()->back();
        });
    }

    public function update(UpdateRequest $request, Admin $staff)
    {
        return $this->try_catch_admin(function () use($request, $staff) {
            $staff = $this->service->update($request, $staff);
            return redirect()->route('admin.staff.show', $staff);
        });
    }

    public function destroy(Admin $staff)
    {
        return $this->try_catch_admin(function () use($staff) {
            $this->service->blocking($staff);
            return redirect()->route('admin.staff.index');
        });
    }

    public function activate(Admin $staff)
    {
        return $this->try_catch_admin(function () use($staff) {
            $this->service->activate($staff);
            return redirect()->route('admin.staff.index');
        });
    }

    public function test(Request $request)
    {
        return response()->json([
            'name' => $request['file'],
        ]);
    }

    //AJAX
    public function response(Request $request, Admin $staff)
    {
        return $this->try_catch_ajax_admin(function () use($request, $staff) {
            $this->service->responsibility((int)$request['code'], $staff);
            return response()->json(true);
        });

    }
}
