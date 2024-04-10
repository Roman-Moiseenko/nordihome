<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\Storage;
use App\Modules\Accounting\Entity\Supply;
use App\Modules\Accounting\Service\SupplyService;
use App\Modules\Order\Entity\Order\OrderItem;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    private SupplyService $service;

    public function __construct(SupplyService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = Supply::orderByDesc('created_at');
            $distributors = Distributor::orderBy('name')->get();
            $storages = Storage::orderBy('name')->get();

            $completed = $request['completed'] ?? 'all';
            if ($completed == 'true') $query->where('completed', '=', true);
            if ($completed == 'false') $query->where('completed', '=', false);
            if (!empty($distributor_id = $request->get('distributor_id'))) {
                $query->where('distributor_id', $distributor_id);
            }
            if (!empty($storage_id = $request->get('storage_id'))) {
                $query->where('storage_id', $storage_id);
            }

            $supplies = $this->pagination($query, $request, $pagination);

            return view('admin.accounting.supply.index',
                compact('supplies', 'pagination', 'completed', 'storages', 'distributors', 'storage_id', 'distributor_id'));
        });
    }

    public function add_stack(Request $request, OrderItem $item)
    {
        $request->validate([
            'storage' => 'required|numeric|min:0|not_in:0',
        ]);
        return $this->try_catch_admin(function () use($request, $item) {
            $stack = $this->service->add_stack($item, (int)$request['storage']);
            if (!empty($stack)) flash('Товар ' . $stack->product->name . ' помещен в стек заказа', 'info');
            return redirect()->back();
        });
    }
}
