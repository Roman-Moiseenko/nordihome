<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Delivery\Entity\DeliveryTruck;
use App\Modules\Delivery\Service\TruckService;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
        //TODO Список грузовиков Название/Грузоподъемность/Объем/Активен/(Активен/Неактивен, Удалить)
        return Inertia::render('Delivery/Truck/Index', [
            'trucks' => $trucks,
        ]);
    }



    public function store(Request $request)
    {
        //TODO Редактирование через Модальное окно
        $request->validate([
            'name' => 'required|string',
        ]);
        $this->service->register($request->all());
        return redirect()->back()->with('success', 'Транспорт добавлен');
    }


    public function update(Request $request, DeliveryTruck $truck)
    {
        $this->service->update($request->all(), $truck);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(DeliveryTruck $truck)
    {
        $this->service->delete($truck);
        return redirect()->back()->with('success', 'Транспорт удален');
    }
}
