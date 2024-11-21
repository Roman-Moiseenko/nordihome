<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Repository\StorageRepository;
use App\Modules\Accounting\Service\StorageService;
use App\UseCase\PaginationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;
use Inertia\Response;

class StorageController extends Controller
{

    private StorageService $service;
    private StorageRepository $repository;

    public function __construct(StorageService $service, StorageRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:admin-panel']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $storages = $this->repository->getIndex($request, $filters);
        return Inertia::render('Accounting/Storage/Index', [
            'storages' => $storages,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        try {
            $storage = $this->service->create($request);
            return redirect()->route('admin.accounting.storage.show', $storage)->with('success', 'Хранилище добавлено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Request $request, Storage $storage): Response
    {
        return Inertia::render('Accounting/Storage/Show', [
            'storage' => $this->repository->StorageWithToArray($storage, $request),
        ]);
    }

    public function set_info(Request $request, Storage $storage): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);
        try {
            $this->service->setInfo($request, $storage);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
