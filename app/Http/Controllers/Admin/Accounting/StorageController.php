<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Events\ThrowableHasAppeared;
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
        try {
            $storages = Storage::orderBy('name')->get();
            return view('admin.accounting.storage.index', compact('storages'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create(Request $request)
    {
        try {
            $organizations = Organization::get();
            return view('admin.accounting.storage.create', compact('organizations'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'organization_id' => 'required|int',
            ]);
            $storage = $this->service->create($request);
            return redirect()->route('admin.accounting.storage.show', $storage);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Request $request, Storage $storage)
    {
        try {
            //TODO поиск по товару ....

            $query = $storage->items();

            if (!empty($search = $request['search'])) {
                $query->whereHas('product', function ($q) use ($search) {
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
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Storage $storage)
    {
        try {
            $organizations = Organization::get();
            return view('admin.accounting.storage.edit', compact('storage', 'organizations'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, Storage $storage)
    {
        try {
            $request->validate([
                'name' => 'required',
                'organization_id' => 'required',
            ]);
            $storage = $this->service->update($request, $storage);
            return redirect()->route('admin.accounting.storage.show', $storage);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
}
