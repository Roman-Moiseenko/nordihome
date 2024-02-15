<?php

namespace App\Http\Controllers\Admin;

use App\Entity\Admin;
use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest;
use App\Http\Requests\Admin\UpdateRequest;
use App\UseCases\Admin\RegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;


class StaffController extends Controller
{
    private RegisterService $service;
    private mixed $pagination;

    /**
     * Display a listing of the resource.
     */

    public function __construct(RegisterService $service)
    {
        $this->middleware(['auth:admin', 'can:user-manager']);
        $this->service = $service;
        $this->pagination = Config::get('shop-config.p-card');
    }

    public function index(Request $request)
    {
        try {
            //TODO в Репозиторий!!!!!
            $query = Admin::orderByDesc('id');
            if (!empty($value = $request->get('role'))) {
                $query->where('role', $value);
            }
            $selected = $request['role'] ?? '';
            $roles = Admin::ROLES;
            $pagination = $request['p'] ?? $this->pagination;
            $admins = $query->paginate($pagination);
            if (isset($request['p'])) {
                $admins->appends(['p' => $pagination]);
            }
            return view('admin.staff.index', compact('admins', 'roles', 'selected', 'pagination'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create()
    {
        try {
            $roles = Admin::ROLES;
            return view('admin.staff.create', compact('roles'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(RegisterRequest $request)
    {
        try {
            $staff = $this->service->register($request);
            return redirect()->route('admin.staff.show', compact('staff'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Admin $staff)
    {
        try {
            return view('admin.staff.show', compact('staff'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Admin $staff)
    {
        try {
            $roles = Admin::ROLES;
            return view('admin.staff.edit', compact('staff', 'roles'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function security(Admin $staff)
    {
        try {
            return view('admin.staff.security', compact('staff'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
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
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(UpdateRequest $request, Admin $staff)
    {
        try {
            $staff = $this->service->update($request, $staff);
            return view('admin.staff.show', compact('staff'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Admin $staff)
    {
        try {
            $this->service->blocking($staff);
            return redirect()->route('admin.staff.index');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function activate(Admin $staff)
    {
        try {
            $this->service->activate($staff);
            return redirect()->route('admin.staff.index');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
/*
    public function setPhoto(Request $request, Admin $staff)
    {
        try {
            $this->service->setPhoto($request->file('file'), $staff);
        } catch (\Throwable $e) {

            return response()->json([$e->getMessage(), $e->getFile(), $e->getLine()]);
        }
        return response()->json([
            'name' => $staff->photo,
        ]);
    }
*/
    public function test(Request $request)
    {
        return response()->json([
            'name' => $request['file'],
        ]);
    }
}
