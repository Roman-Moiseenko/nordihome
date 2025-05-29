<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Entity\Worker;
use App\Modules\Delivery\Entity\DeliveryCargo;
use App\Modules\Delivery\Repository\DeliveryRepository;
use App\Modules\Delivery\Service\CalendarService;
use App\Modules\Delivery\Service\DeliveryService;
use App\Modules\Guide\Entity\CargoCompany;
use App\Modules\Order\Entity\Order\OrderExpense;
use App\Modules\Order\Service\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

/**
 * Контроллер работы с выдачей товара OrderExpense и доставкой для Склада
 */
class DeliveryController extends Controller
{
    private ExpenseService $expenseService;
    private DeliveryRepository $repository;
    private CalendarService $calendarService;
    private DeliveryService $service;

    public function __construct(
        ExpenseService $expenseService,
        DeliveryRepository $repository,
        CalendarService $calendarService,
        DeliveryService $service,
    )
    {
        $this->middleware(['auth:admin', 'can:delivery', 'can:order']);
        $this->expenseService = $expenseService;
        $this->repository = $repository;
        $this->calendarService = $calendarService;
        $this->service = $service;
    }

    /**
     * Все распоряжения, кроме отмененных
     */
    public function all(Request $request): Response
    {
        $expenses = $this->repository->getAll($request, $filters);
        return Inertia::render('Delivery/All/Index', [
            'expenses' => $expenses,
            'filters' => $filters,
            'works' => Worker::where('active', true)->get(),
        ]);
    }

    /**
     * Распоряжения на упаковку
     */
    public function to_loader(Request $request): Response
    {
        $expenses = $this->repository->getLoader($request);
        return Inertia::render('Delivery/Loader/Index', [
            'expenses' => $expenses,
            'works' => Worker::where('active', true)->getModels(),
        ]);
    }

    /**
     * Распоряжения на доставку
     */
    public function to_delivery(Request $request): Response
    {
        $local = $this->repository->getCalendar();
        $incomplete = $this->repository->getCalendar(false);
        $region = $this->repository->getRegion();
        $ozon = $this->repository->getOzon();
        return Inertia::render('Delivery/Delivery/Index', [
            'local' => $local,
            'incomplete' => $incomplete,
            'region' => $region,
            'ozon' => $ozon,
            'cargo_companies' => CargoCompany::orderBy('name')->getModels(),
            'drivers' => Worker::where('active', true)->where('driver', true)->get(),
            'assembles' => Worker::where('active', true)->where('assemble', true)->get(),
        ]);
    }

   //Назначение рабочих
    /**
     * Назначаем упаковщика => Груз (распоряжение) собирается
     */
    public function set_loader(Request $request, OrderExpense $expense): RedirectResponse
    {
        $this->expenseService->setLoader($expense, $request->integer('worker_id'));
        return redirect()->back()->with('success', 'Упаковщик назначен');
    }

    /**
     * Отменяем упаковщика => Груз (распоряжение) ждет сборщика
     */
    public function del_loader(Request $request, OrderExpense $expense): RedirectResponse
    {
        $this->expenseService->delLoader($expense);
        return redirect()->back()->with('success', 'Упаковщик отменен');
    }

    /**
     * Назначаем доставщика => Груз (распоряжение) на доставке
     */
    public function set_driver(Request $request, OrderExpense $expense): RedirectResponse
    {

        $this->expenseService->setDriver($expense, $request->integer('worker_id'));
        return redirect()->back()->with('success', 'Доставщик назначен');
    }

    /**
     * Отменяем доставщика => Груз (распоряжение) ждет доставщика
     */
    public function del_driver(Request $request, OrderExpense $expense): RedirectResponse
    {
        $this->expenseService->delDriver($expense);
        return redirect()->back()->with('success', 'Доставщик отменен');
    }


    /**
     * Назначаем Сборщика мебели => Статус не меняется, для учета работ.
     */
    public function set_assemble(Request $request, OrderExpense $expense): RedirectResponse
    {
        $this->expenseService->setAssemble($expense, $request->input('worker_id', []));
        return redirect()->back()->with('success', 'Сборщик назначен');
    }

    /**
     * Отменяем Сборщика мебели => Статус не меняется
     */
    public function del_assemble(Request $request, OrderExpense $expense): RedirectResponse
    {
        $this->expenseService->delAssemble($expense);
        return redirect()->back()->with('success', 'Сборщик отменен');
    }

    /**
     * Груз (распоряжение) выдан
     */
    public function completed(OrderExpense $expense): RedirectResponse
    {
        $this->expenseService->completed($expense);
        return redirect()->back()->with('success', 'Распоряжение выдано');
    }


    /**
     * Груз (распоряжение) собран (ожидает доставки)
     */
    public function assembled(OrderExpense $expense): RedirectResponse
    {
        $this->expenseService->assembled($expense);
        return redirect()->back()->with('success', 'Распоряжение собрано');
    }

    public function set_period(OrderExpense $expense, Request $request): RedirectResponse
    {
        $this->calendarService->attach_expense($expense, $request->integer('period_id'));
        return redirect()->back()->with('success', 'Назначена новая дата отгрузки');
    }

    public function set_cargo(OrderExpense $expense, Request $request): RedirectResponse
    {
        $this->service->create($expense, $request);
        return redirect()->back()->with('success', 'Трек номер и ТК установлены');
    }

    public function set_complete(OrderExpense $expense): RedirectResponse
    {
        $this->service->setStatus($expense, DeliveryCargo::STATUS_ISSUED);
        return redirect()->back()->with('success', 'Посылка выдана');
    }

    //TODO Заменить для доставки ТК на setTrackNumber
    #[Deprecated]
    public function delivery(Request $request, OrderExpense $expense)
    {
        $this->expenseService->delivery($expense, $request['track'] ?? '');
        return redirect()->back();
    }
}
