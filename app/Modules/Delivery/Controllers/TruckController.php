<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Delivery\Entity\DeliveryTruck;
use App\Modules\Delivery\Repository\TruckRepository;
use App\Modules\Delivery\Service\TruckService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TruckController extends Controller
{
    private TruckService $service;
    private TruckRepository $repository;

    public function __construct(TruckService $service, TruckRepository $repository)
    {
        $this->middleware(['auth:admin']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): Response
    {
        $trucks = $this->repository->getIndex($request);
        return Inertia::render('Delivery/Truck/Index', [
            'trucks' => $trucks,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $this->service->register($request);
        return redirect()->back()->with('success', 'Транспорт добавлен');
    }


    public function set_info(Request $request, DeliveryTruck $truck): RedirectResponse
    {
        $this->service->setInfo($request, $truck);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function toggle(DeliveryTruck $truck): RedirectResponse
    {
        if ($truck->isActive()) {
            $message = 'Транспорт убран из расчета доставки';
            $truck->draft();
        } else {
            $message = 'Транспорт добавлен в расчет доставки';
            $truck->active();
        }
        return redirect()->back()->with('success', $message);
    }

    public function destroy(DeliveryTruck $truck): RedirectResponse
    {
        $this->service->delete($truck);
        return redirect()->back()->with('success', 'Транспорт удален');
    }
}
