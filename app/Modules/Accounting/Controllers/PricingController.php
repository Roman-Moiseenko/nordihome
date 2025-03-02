<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\ArrivalDocument;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\PricingDocument;
use App\Modules\Accounting\Entity\PricingProduct;
use App\Modules\Accounting\Repository\PricingRepository;
use App\Modules\Accounting\Service\PricingService;
use App\Modules\Admin\Repository\StaffRepository;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\ProductRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;


class PricingController extends Controller
{

    private PricingService $service;
    private PricingRepository $repository;
    private StaffRepository $staffs;

    public function __construct(
        PricingService    $service,
        PricingRepository $repository,
        StaffRepository   $staffs,
    )
    {
        $this->middleware(['auth:admin', 'can:pricing']);
        $this->middleware(['auth:admin', 'can:admin-panel'])->only(['work', 'destroy']);
        $this->service = $service;
        $this->repository = $repository;
        $this->staffs = $staffs;
    }

    public function index(Request $request): \Inertia\Response
    {
        $pricings = $this->repository->getIndex($request, $filters);
        $staffs = $this->staffs->getStaffsChiefs();
        $distributors = Distributor::orderBy('name')->get();
        return Inertia::render('Accounting/Pricing/Index', [
            'pricings' => $pricings,
            'filters' => $filters,
            'distributors' => $distributors,
            'staffs' => $staffs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pricing = $this->service->create();
        return redirect()->route('admin.accounting.pricing.show', $pricing); //view('admin.accounting.pricing.create');
    }

    public function show(PricingDocument $pricing, Request $request)
    {
        //return view('admin.accounting.pricing.show', compact('pricing'));
        return Inertia::render('Accounting/Pricing/Show', [
            'pricing' => $this->repository->PricingWithToArray($pricing, $request, $filters),
            'filters' => $filters,
        ]);
    }

    public function destroy(PricingDocument $pricing): RedirectResponse
    {
        $this->service->destroy($pricing);
        return redirect()->back()->with('success', 'Ценообразование помечено на удаление');
    }

    public function restore(PricingDocument $pricing): RedirectResponse
    {
        $this->service->restore($pricing);
        return redirect()->back()->with('success', 'Ценообразование восстановлено');
    }

    public function full_destroy(PricingDocument $pricing): RedirectResponse
    {
        $this->service->fullDestroy($pricing);
        return redirect()->back()->with('success', 'Ценообразование удалено окончательно');
    }

    public function completed(PricingDocument $pricing): RedirectResponse
    {
        $this->service->completed($pricing);
        return redirect()->back()->with('success', 'Документ проведен');
    }

    public function work(PricingDocument $pricing): RedirectResponse
    {
        $this->service->work($pricing);
        return redirect()->back()->with('success', 'Документ в работе');
    }

    public function set_info(PricingDocument $pricing, Request $request): RedirectResponse
    {
        $this->service->setInfo($pricing, $request);
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function del_product(PricingProduct $product)
    {
        $this->service->delProduct($product);
        return redirect()->back()->with('success', 'Удалено');;
    }

    public function add_product(Request $request, PricingDocument $pricing): RedirectResponse
    {
        $this->service->addProduct($pricing, $request->integer('product_id'));
        return redirect()->back()->with('success', 'Товар добавлен');
    }

    public function add_products(Request $request, PricingDocument $pricing): RedirectResponse
    {
        $this->service->addProducts($pricing, $request->input('products'));
        return redirect()->back()->with('success', 'Товары добавлены');
    }

    public function set_product(PricingProduct $product, Request $request): RedirectResponse
    {
        $this->service->setProduct($product, $request->all());
        return redirect()->back()->with('success', 'Сохранено');
    }

    public function copy(PricingDocument $pricing): RedirectResponse
    {
        $pricing = $this->service->copy($pricing);
        return redirect()->route('admin.accounting.pricing.show', $pricing)->with('success', 'Документ скопирован');
    }
}
