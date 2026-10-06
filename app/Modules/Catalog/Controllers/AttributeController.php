<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Infrastructure\Models\Attribute;
use App\Modules\Catalog\Repository\AttributeGroupRepository;
use App\Modules\Catalog\Repository\AttributeRepository;
use App\Modules\Catalog\Service\AttributeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttributeController extends Controller
{

    private AttributeService $service;

    private AttributeRepository $repository;
    private AttributeGroupRepository $groupRepository;


    public function __construct(
        AttributeService         $service,
        AttributeRepository      $repository,
        AttributeGroupRepository $groupRepository,
    )
    {
        $this->service = $service;
        $this->repository = $repository;
        $this->groupRepository = $groupRepository;
    }

    public function index(Request $request): Response
    {
        $groups = $this->groupRepository->get(order_by: 'name');
        $attributes = $this->repository->getIndex($request, $filters);
        return Inertia::render('Catalog/Attribute/Index', [
            'attributes' => $attributes,
            'filters' => $filters,
            'groups' => $groups,
            'types' => array_select(Attribute::ATTRIBUTES),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'categories' => 'required|array',
            'group_id' => 'required|integer',
            'name' => 'required|string',
            'type' => 'required|integer',
        ]);
        try {
            $attribute = $this->service->create($request);
            return redirect()->route('admin.catalog.attribute.show', $attribute)->with('success', 'Атрибут создан');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show(Attribute $attribute): Response
    {
        $groups = $this->groupRepository->get(order_by: 'name');
        return Inertia::render('Catalog/Attribute/Show', [
            'attribute' => $this->repository->AttributeWithToArray($attribute),
            'groups' => $groups,
            'types' => array_select(Attribute::ATTRIBUTES),
            'variant' => Attribute::TYPE_VARIANT,
        ]);
    }

    public function set_info(Request $request, Attribute $attribute): RedirectResponse
    {
        try {
            $this->service->setInfo($request, $attribute);
            return redirect()->back()->with('success', 'Сохранено');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        try {
            $this->service->delete($attribute);
            return redirect()->back()->with('success', 'Атрибут удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /*
    //Варианты
    public function variant_image(Request $request, AttributeVariant $variant)
    {
        $this->service->image_variant($variant, $request);
        return redirect()->back();
    }*/
}
