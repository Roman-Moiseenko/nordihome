<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest;
use App\Http\Requests\Admin\UpdateRequest;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Admin\Service\StaffService;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;


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
        $this->middleware(['auth:admin', 'can:staff'])->except(['notification', 'notification_read']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $selected = $request['role'] ?? '';
        $roles = Admin::ROLES;
        $query = $this->repository->getIndex($request);
        $admins = $this->pagination($query, $request, $pagination);
        return view('admin.staff.index', compact('admins', 'roles', 'selected', 'pagination'));
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
        $this->service->setPassword($request['password'], $staff);
        flash('Пароль успешно изменен', 'success');
        return redirect()->back();
    }

    public function update(UpdateRequest $request, Admin $staff)
    {
        $this->service->update($request, $staff);
        return redirect()->route('admin.staff.show', $staff);
    }

    public function destroy(Admin $staff)
    {
        $this->service->blocking($staff);
        return redirect()->route('admin.staff.index');
    }

    public function activate(Admin $staff)
    {
        $this->service->activate($staff);
        return redirect()->route('admin.staff.index');
    }

    public function notification(Request $request)
    {
        /** @var Admin $staff */
        $staff = Auth::guard('admin')->user();
        $query = $staff->notifications();
        $notifications = $this->pagination($query, $request, $pagination);

        return view('admin.staff.notification', compact('notifications', 'pagination'));
    }

    public function notification_read(DatabaseNotification $notification)
    {
        $notification->markAsRead();
        return response()->json(true);
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
        $this->service->responsibility($request->integer('code'), $staff);
        return response()->json(true);
    }
}
