<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\StorageService;
use App\UseCase\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class StorageController extends Controller
{

    private StorageService $service;

    public function __construct(StorageService $service)
    {
        $this->middleware(['auth:admin', 'can:admin-panel']);
        $this->service = $service;
    }

    public function index()
    {
            $storages = Storage::orderBy('name')->get();
            return view('admin.accounting.storage.index', compact('storages'));
    }

    public function create(Request $request)
    {
            $organizations = Organization::get();
            return view('admin.accounting.storage.create', compact('organizations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'organization_id' => 'required|int',
        ]);
            $storage = $this->service->create($request);
            return redirect()->route('admin.accounting.storage.show', $storage);
    }

    public function show(Request $request, Storage $storage)
    {
            $query = $storage->items();
            $items = $this->pagination($query, $request, $pagination);
            return view('admin.accounting.storage.show', compact('storage', 'items', 'pagination'));
    }

    public function edit(Storage $storage)
    {
            $organizations = Organization::get();
            return view('admin.accounting.storage.edit', compact('storage', 'organizations'));
    }

    public function update(Request $request, Storage $storage)
    {
        $request->validate([
            'name' => 'required',
            'organization_id' => 'required',
        ]);
            $this->service->update($request, $storage);
            return redirect()->route('admin.accounting.storage.show', $storage);
    }
}
