<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\_old_modules\Delivery;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Delivery\Entity\DeliveryTruck;
use App\Modules\Delivery\Service\TruckService;
use Illuminate\Http\Request;
use function redirect;
use function view;

class TruckController extends Controller
{
    private TruckService $service;

    public function __construct(TruckService $service)
    {
        $this->middleware(['auth:admin']);

        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $query = DeliveryTruck::orderBy('name')->where('active', true);
            $trucks = $this->pagination($query, $request, $pagination);
            return view('admin.delivery.truck.index', compact('trucks', 'pagination'));
        });
    }

    public function create()
    {
        $drivers = Worker::where('post', Worker::DRIVER)->where('active', true)->get();
        return view('admin.delivery.truck.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        return $this->try_catch_admin(function () use($request) {
            $truck = $this->service->register($request->all());
            return redirect()->route('admin.delivery.truck.show', compact('truck'));
        });
    }

    public function show(DeliveryTruck $truck)
    {
        return $this->try_catch_admin(function () use($truck) {
            return view('admin.delivery.truck.show', compact('truck'));
        });
    }

    public function edit(DeliveryTruck $truck)
    {
        return $this->try_catch_admin(function () use($truck) {
            $drivers = Worker::where('post', Worker::DRIVER)->where('active', true)->get();
            return view('admin.delivery.truck.edit', compact('truck', 'drivers'));
        });
    }

    public function update(Request $request, DeliveryTruck $truck)
    {
        return $this->try_catch_admin(function () use($request, $truck) {
            $this->service->update($request->all(), $truck);
            return redirect()->route('admin.delivery.truck.show', compact('truck'));
        });
    }

    public function destroy(DeliveryTruck $truck)
    {
        return $this->try_catch_admin(function () use($truck) {
            $this->service->delete($truck);
            return redirect()->route('admin.delivery.truck.index');
        });
    }
}
