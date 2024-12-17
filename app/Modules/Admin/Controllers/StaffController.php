<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRequest;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Responsibility;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Admin\Request\PasswordRequest;
use App\Modules\Admin\Request\StaffCreateRequest;
use App\Modules\Admin\Request\StaffUpdateRequest;
use App\Modules\Admin\Service\StaffService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

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
        $admins = $this->repository->getIndex($request, $filters);
        return Inertia::render('Admin/Staff/Index', [
            'staffs' => $admins,
            'filters' => $filters,
            'roles' => array_select(Admin::ROLES),
        ]);
    }

    public function create()
    {
        $roles = Admin::ROLES;
        return view('admin.staff.create', compact('roles'));
    }

    public function store(StaffCreateRequest $request)
    {
        $staff = $this->service->register($request);
        return redirect()->route('admin.staff.show', compact('staff'));
    }

    public function show(Admin $staff): Response
    {
        return Inertia::render('Admin/Staff/Show', [
            'staff' => $this->repository->StaffWithToArray($staff),
            'roles' => array_select(Admin::ROLES),
            'responsibilities' => array_select(Responsibility::RESPONSE),
        ]);
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

    public function password(PasswordRequest $request, Admin $staff): RedirectResponse
    {
        try {
            $this->service->setPassword($request['password'], $staff);
            return redirect()->back()->with('success', 'Пароль успешно изменен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(StaffUpdateRequest $request, Admin $staff)
    {
        try {
            $this->service->update($request, $staff);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Admin $staff): RedirectResponse
    {
        $this->service->blocking($staff);
        return redirect()->back()->with('success', 'Заблокировано');
    }

    public function activate(Admin $staff): RedirectResponse
    {
        $this->service->activate($staff);
        return redirect()->back()->with('success', 'Активирован');
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

    public function responsibility(Request $request, Admin $staff): RedirectResponse
    {
        $this->service->responsibility($request->integer('code'), $staff);
        return redirect()->back()->with('success', 'Сохранено');
    }

}
