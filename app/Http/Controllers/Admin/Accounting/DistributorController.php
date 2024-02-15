<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Events\ThrowableHasAppeared;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Service\DistributorService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class DistributorController extends Controller
{
    private mixed $pagination;
    private DistributorService $service;

    public function __construct(DistributorService $service)
    {
        $this->service = $service;
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        try {
            $query = Distributor::orderBy('name');

            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $distributors = $query->paginate($pagination);
                $distributors->appends(['p' => $pagination]);
            } else {
                $distributors = $query->paginate($this->pagination);
            }
            return view('admin.accounting.distributor.index', compact('distributors', 'pagination'));

        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create(Request $request)
    {
        try {
            return view('admin.accounting.distributor.create');
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
            ]);
            $distributor = $this->service->create($request);
            return redirect()->route('admin.accounting.distributor.show', $distributor);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Distributor $distributor)
    {
        try {
            //TODO В дальнейшем добавить список товаров
            return view('admin.accounting.distributor.show', compact('distributor'));

        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Distributor $distributor)
    {
        try {
            return view('admin.accounting.distributor.edit', compact('distributor'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, Distributor $distributor)
    {
        try {
            $request->validate([
                'name' => 'required',
            ]);
            $distributor = $this->service->update($request, $distributor);
            return redirect()->route('admin.accounting.distributor.show', $distributor);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Distributor $distributor)
    {
        try {
            $this->service->destroy($distributor);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }
}
