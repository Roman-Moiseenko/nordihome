<?php

namespace App\Modules\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Employee\Entity\Employee;
use App\Modules\Notification\Entity\Notification;
use App\Modules\Notification\Helpers\NotificationHelper;
use App\Modules\Notification\Requests\NotificationRequest;
use App\Modules\Notification\Repository\NotificationRepository;
use App\Modules\Notification\Service\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use JetBrains\PhpStorm\Deprecated;

class NotificationController extends Controller
{

    private NotificationService $service;
    private NotificationRepository $repository;

    public function __construct(NotificationService $service, NotificationRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }


    public function index(Request $request)
    {
        /** @var Admin $admin */
        $admin = Auth::guard('admin')->user();
        $notifications = $this->repository->getIndex($request, $filters);

        return Inertia::render('Notification/Notification/Index', [
                'notifications' => $notifications,
                'chief' => $admin->isAdmin() || $admin->isChief(),
                'filters' => $filters,
                'events' => array_select(NotificationHelper::EVENTS),
            ]
        );
    }

    public function read(DatabaseNotification $notification)
    {
        $notification->markAsRead();
        return redirect()->back()->with('success', 'Прочитано');
    }

    public function create(Request $request)
    {
        $staffs = Admin::where('active', true)->get()->map(function (Admin $staff) {
            return [
                'id' => $staff->id,
                'telegram_id' => $staff->telegram_user_id,
                'name' => $staff->fullname->getFullName(),
            ];
        })->toArray();
        $workers = Worker::where('active', true)->get()->map(function (Worker $worker) {
            return [
                'id' => $worker->id,
                'telegram_id' => $worker->telegram_user_id,
                'name' => $worker->fullname->getFullName(),
            ];
        })->toArray();
        return Inertia::render('Notification/Notification/Create', [
            'staffs' => $staffs,
            'workers' => $workers,
        ]);
    }

    public function store(NotificationRequest $request)
    {
        $request->validated();
        $this->service->create($request);
        return redirect()
            ->route('admin.notification.notification.index')
            ->with('success', 'Уведомление отправлено');
    }


}
