<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Delivery;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Delivery\Entity\DeliveryOrder;
use App\UseCase\PaginationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DeliveryController extends Controller
{
    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            return $this->view($request);
        });
    }

    public function index_local(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            return $this->view($request, DeliveryOrder::LOCAL);
        });
    }

    public function index_region(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            return $this->view($request, DeliveryOrder::REGION);
        });
    }

    public function index_storage(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            return $this->view($request, DeliveryOrder::STORAGE);
        });
    }

    private function view(Request $request, $type = null)
    {
        return $this->try_catch_admin(function () use($request, $type) {
            $status = $request['status'] ?? '';
            $query = DeliveryOrder::orderByDesc('created_at');
            if ($type != null) $query = $query->where('type', $type);

            if ($request->has('status')) {
                $query->whereHas('status', function ($q) use ($request) {
                    $q->where('value', $request->get('status'));
                });
            }

            $deliveries = $this->pagination($query, $request, $pagination);
            return view('admin.delivery.index', compact('deliveries', 'type', 'status', 'pagination'));
        });
    }
}
