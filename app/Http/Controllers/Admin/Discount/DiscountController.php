<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Discount;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Discount\Entity\Discount;
use App\Modules\Discount\Service\DiscountService;
use Illuminate\Http\Request;

class DiscountController extends Controller
{

    private DiscountService $service;

    public function __construct(DiscountService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return $this->try_catch_admin(function () use($request) {
            $discounts = Discount::orderBy('name')->get();
            return view('admin.discount.discount.index', compact('discounts'));
        });
    }

    public function create()
    {
        return $this->try_catch_admin(function () {
            return view('admin.discount.discount.create');
        });
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'discount' => 'required|int',
            'class' => 'required|string',
            '_from' => 'required',
        ]);

        return $this->try_catch_admin(function () use($request) {
            $discount = $this->service->create($request);
            return redirect()->route('admin.discount.discount.show', compact('discount'));
        });
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
        return $this->try_catch_admin(function () use($request, $discount) {
            $discount = $this->service->update($request, $discount);
            return redirect()->route('admin.discount.discount.show', compact('discount'));
        });
    }

    public function destroy(Discount $discount)
    {
        return $this->try_catch_admin(function () use($discount) {
            $this->service->delete($discount);
            return redirect()->route('admin.discount.discount.index');
        });
    }

    //Команды
    public function draft(Discount $discount)
    {
        return $this->try_catch_admin(function () use($discount) {
            $this->service->draft($discount);
            return back();
        });
    }

    public function published(Discount $discount)
    {
        return $this->try_catch_admin(function () use($discount) {
            $this->service->published($discount);
            return back();
        });
    }

    //AJAX
    public function widget(Request $request)
    {
        return $this->try_catch_ajax_admin(function () use($request) {
            $class = Discount::namespace() . '\\' . $request['class'];
            return \response()->json($class::widget());
        });
    }
}
