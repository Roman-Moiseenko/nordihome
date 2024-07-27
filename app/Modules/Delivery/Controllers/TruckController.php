<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Delivery\Entity\DeliveryTruck;
use App\Modules\Delivery\Service\TruckService;
use Illuminate\Http\Request;

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
        $query = DeliveryTruck::orderBy('name')->where('active', true);
        $trucks = $this->pagination($query, $request, $pagination);
        return view('admin.delivery.truck.index', compact('trucks', 'pagination'));
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
        $truck = $this->service->register($request->all());
        return redirect()->route('admin.delivery.truck.show', compact('truck'));
    }

    public function show(DeliveryTruck $truck)
    {
        return view('admin.delivery.truck.show', compact('truck'));
    }

    public function edit(DeliveryTruck $truck)
    {
        $drivers = Worker::where('post', Worker::DRIVER)->where('active', true)->get();
        return view('admin.delivery.truck.edit', compact('truck', 'drivers'));
    }

    public function update(Request $request, DeliveryTruck $truck)
    {
        $this->service->update($request->all(), $truck);
        return redirect()->route('admin.delivery.truck.show', compact('truck'));
    }

    public function destroy(DeliveryTruck $truck)
    {
        $this->service->delete($truck);
        return redirect()->route('admin.delivery.truck.index');
    }
}
