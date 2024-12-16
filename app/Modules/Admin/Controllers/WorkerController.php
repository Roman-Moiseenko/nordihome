<?php
declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Admin\Repository\WorkerRepository;
use App\Modules\Admin\Service\WorkerService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkerController extends Controller
{
    private WorkerService $service;
    private WorkerRepository $repository;

    public function __construct(WorkerService $service, WorkerRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:staff']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): \Inertia\Response
    {
        $workers = $this->repository->getIndex($request, $filters);
        return Inertia::render('Admin/Worker/Index', [
            'workers' => $workers,
            'filters' => $filters,
            'storages' => Storage::orderBy('name')->getModels(),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $worker = $this->service->register($request);
            return redirect()->route('admin.worker.show', $worker)->with('success', 'Рабочий добавлен');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Worker $worker)
    {
        return Inertia::render('Admin/Worker/Show', [
            'worker' => $this->repository->WorkerWithToArray($worker),
            'storages' => Storage::orderBy('name')->getModels(),
        ]);
    }


    public function update(Request $request, Worker $worker)
    {
        try {
            $this->service->update($request, $worker);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Worker $worker): \Illuminate\Http\RedirectResponse
    {
        $this->service->destroy($worker);
        return redirect()->back()->with('success', 'Удалено');
    }

    public function toggle(Worker $worker): \Illuminate\Http\RedirectResponse
    {
        $this->service->toggle($worker);
        return redirect()->back()->with('success', 'Сохранено');
    }
}
