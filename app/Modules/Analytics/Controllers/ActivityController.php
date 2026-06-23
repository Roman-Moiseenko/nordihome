<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Admin;
use App\Modules\Analytics\Entity\LoggerActivity;
use App\Modules\Analytics\Entity\LoggerCron;
use App\Modules\Analytics\Repository\ActivityRepository;
use App\Modules\Auth\Infrastructure\Models\Staff;
use App\UseCase\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;

class ActivityController extends Controller
{
    private ActivityRepository $repository;

    public function __construct(ActivityRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request): \Inertia\Response
    {
        $activities = $this->repository->getIndex($request, $filters);

        $staffs = Staff::get()->toArray();
        return Inertia::render('Analytics/Activity/Index', [
            'activities' => $activities,
            'filters' => $filters,
            'staffs' => $staffs,
        ]);
    }
}
