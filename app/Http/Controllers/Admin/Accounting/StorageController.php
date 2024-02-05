<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Modules\Accounting\Entity\Organization;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Service\StorageService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class StorageController extends Controller
{

    private StorageService $service;
    private mixed $pagination;

    public function __construct(StorageService $service)
    {
        $this->service = $service;
        $this->pagination = Config::get('shop-config.p-list');
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
        //TODO поиск по товару ....

        $query = $storage->items();

        if (!empty($search = $request['search'])) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('code_search', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "% {$search}%");
            });

        }
        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $items = $query->paginate($pagination);
            $items->appends(['p' => $pagination]);
        } else {
            $items = $query->paginate($this->pagination);
        }

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
        $storage = $this->service->update($request, $storage);
        return redirect()->route('admin.accounting.storage.show', $storage);
    }
}
