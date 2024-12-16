<?php
declare(strict_types=1);

namespace App\Modules\Discount\Controllers;

use App\Events\ThrowableHasAppeared;
use App\Http\Controllers\Controller;
use App\Modules\Discount\Entity\Discount;
use App\Modules\Discount\Helpers\DiscountHelper;
use App\Modules\Discount\Repository\DiscountRepository;
use App\Modules\Discount\Service\DiscountService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscountController extends Controller
{

    private DiscountService $service;
    private DiscountRepository $repository;

    public function __construct(DiscountService $service, DiscountRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:discount']);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index(Request $request): \Inertia\Response
    {
        $discounts = $this->repository->getIndex($request);
        return Inertia::render('Discount/Discount/Index', [
            'discounts' => $discounts,
            'types' => array_select(DiscountHelper::discounts()),
        ]);
    }

    public function create()
    {
        return view('admin.discount.discount.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'class' => 'required|string',
        ]);

        $discount = $this->service->create($request);
        return redirect()->route('admin.discount.discount.show', compact('discount'));
    }

    public function show(Discount $discount): \Inertia\Response
    {
        return Inertia::render('Discount/Discount/Show', [
            'discount' => $this->repository->DiscountWithToArray($discount),
        ]);
    }

    public function edit(Discount $discount)
    {
        return view('admin.discount.discount.edit', compact('discount'));
    }

    public function set_info(Request $request, Discount $discount)
    {
        $this->service->setInfo($request, $discount);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function destroy(Discount $discount)
    {
        $this->service->delete($discount);
        return redirect()->route('admin.discount.discount.index');
    }

    //Команды
    public function toggle(Discount $discount)
    {
        if ($discount->isActive()){
            $this->service->draft($discount);
            $message = 'Скидка отключена';
        } else {
            $this->service->published($discount);
            $message = 'Скидка активирована';
        }
        return redirect()->back()->with('success', $message);
    }


    //AJAX
    public function widget(Request $request)
    {
        $class = Discount::namespace() . '\\' . $request['class'];
        return \response()->json($class::widget());
    }
}
