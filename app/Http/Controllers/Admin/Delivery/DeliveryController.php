<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Delivery;

use App\Http\Controllers\Controller;
use App\Modules\Delivery\Entity\DeliveryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DeliveryController extends Controller
{
    private mixed $pagination;

    public function __construct()
    {
        $this->pagination = Config::get('shop-config.p-list');
    }

    public function index(Request $request)
    {
        return $this->view($request);
    }

    public function index_local(Request $request)
    {
        return $this->view($request, DeliveryOrder::LOCAL);
    }

    public function index_region(Request $request)
    {
        return $this->view($request, DeliveryOrder::REGION);
    }

    public function index_storage(Request $request)
    {
        return $this->view($request, DeliveryOrder::STORAGE);
    }

    private function view(Request $request, $type = null)
    {
        $status = $request['status'] ?? '';
        $query = DeliveryOrder::orderByDesc('created_at');
        if ($type != null) $query = $query->where('type', $type);

        if ($request->has('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('value', $request->get('status'));
            });
        }

        //ПАГИНАЦИЯ
        if (!empty($pagination = $request->get('p'))) {
            $deliveries = $query->paginate($pagination);
            $deliveries->appends(['p' => $pagination]);
        } else {
            $deliveries = $query->paginate($this->pagination);
        }

        return view('admin.delivery.index', compact('deliveries', 'type', 'status', 'pagination'));
    }
}
