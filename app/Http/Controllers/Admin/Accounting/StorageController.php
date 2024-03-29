<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

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
        return $this->try_catch_admin(function () {
            $storages = Storage::orderBy('name')->get();
            return view('admin.accounting.storage.index', compact('storages'));
        });
    }

    public function create(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $organizations = Organization::get();
            return view('admin.accounting.storage.create', compact('organizations'));
        });
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'organization_id' => 'required|int',
        ]);
        return $this->try_catch_admin(function () use($request) {
            $storage = $this->service->create($request);
            return redirect()->route('admin.accounting.storage.show', $storage);
        });
    }

    public function show(Request $request, Storage $storage)
    {
        return $this->try_catch_admin(function () use($request, $storage) {
            //TODO поиск по товару ....
            $query = $storage->items();
            if (!empty($search = $request['search'])) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('code_search', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "% {$search}%");
                });
            }
            $items = $this->pagination($query, $request, $pagination);
            return view('admin.accounting.storage.show', compact('storage', 'items', 'pagination'));
        });
    }

    public function edit(Storage $storage)
    {
        return $this->try_catch_admin(function () use($storage) {
            $organizations = Organization::get();
            return view('admin.accounting.storage.edit', compact('storage', 'organizations'));
        });
    }

    public function update(Request $request, Storage $storage)
    {
        $request->validate([
            'name' => 'required',
            'organization_id' => 'required',
        ]);
        return $this->try_catch_admin(function () use($request, $storage) {
            $storage = $this->service->update($request, $storage);
            return redirect()->route('admin.accounting.storage.show', $storage);
        });
    }
}
