<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Repository\ActivityRepository;
use App\Modules\Auth\Infrastructure\Models\Staff;
use Illuminate\Http\Request;
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
