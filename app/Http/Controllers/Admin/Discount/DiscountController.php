<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Discount;

use App\Events\ThrowableHasAppeared;
use App\Modules\Discount\Entity\Discount;
use App\Modules\Discount\Service\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DiscountController extends Controller
{

    private DiscountService $service;

    public function __construct(DiscountService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        try {

            $discounts = Discount::orderBy('name')->get();
            return view('admin.discount.discount.index', compact('discounts'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function create()
    {
        try {
        return view('admin.discount.discount.create');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'discount' => 'required|int',
            'class' => 'required|string',
            '_from' => 'required',
        ]);

        try {
            $discount = $this->service->create($request);
            return redirect()->route('admin.discount.discount.show', compact('discount'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function show(Discount $discount)
    {


        return view('admin.discount.discount.show', compact('discount'));
    }

    public function edit(Discount $discount)
    {
        return view('admin.discount.discount.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        try {
            $discount = $this->service->update($request, $discount);
            return redirect()->route('admin.discount.discount.show', compact('discount'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }

    }

    public function destroy(Discount $discount)
    {
        try {
            $this->service->delete($discount);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
            return back();
        }
        return redirect()->route('admin.discount.discount.index');
    }

    //Команды
    public function draft(Discount $discount)
    {
        try {
            $this->service->draft($discount);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return back();
    }

    public function published(Discount $discount)
    {
        try {
            $this->service->published($discount);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        }
        return back();
    }

    public function widget(Request $request)
    {
        $class = Discount::namespace() . '\\' . $request['class'];
        return \response()->json($class::widget());

    }
}
