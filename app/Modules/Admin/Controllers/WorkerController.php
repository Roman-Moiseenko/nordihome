<?php
declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Admin\Repository\WorkerRepository;
use App\Modules\Admin\Service\WorkerService;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    private WorkerService $service;
    private WorkerRepository $repository;

    public function __construct(WorkerService $service, WorkerRepository $repository)
    {
        //$this->middleware(['auth:admin', 'can:staff']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $selected = $request['post'] ?? '';
        $posts = Worker::POSTS;
        $query = $this->repository->getIndex($request);
        $workers = $this->pagination($query, $request, $pagination);

        return view('admin.worker.index', compact('workers', 'posts', 'selected', 'pagination'));
    }

    public function create()
    {
        $posts = Worker::POSTS;
        $storages = Storage::orderBy('name')->get();
        return view('admin.worker.create', compact('posts', 'storages'));
    }

    public function store(Request $request)
    {
        $worker = $this->service->register($request);
        return redirect()->route('admin.worker.show', compact('worker'));
    }

    public function show(Worker $worker)
    {
        return view('admin.worker.show', compact('worker'));
    }

    public function edit(Worker $worker)
    {
        $posts = Worker::POSTS;
        $storages = Storage::orderBy('name')->get();
        return view('admin.worker.edit', compact('worker', 'posts', 'storages'));
    }

    public function update(Request $request, Worker $worker)
    {
        $worker = $this->service->update($request, $worker);
        return redirect()->route('admin.worker.show', compact('worker'));
    }

    public function destroy(Worker $worker)
    {
        $this->service->destroy($worker);
        return redirect()->route('admin.worker.index');
    }

    public function toggle(Worker $worker)
    {
        $this->service->toggle($worker);
        return redirect()->route('admin.worker.index');
    }
}
