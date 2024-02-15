<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Events\ThrowableHasAppeared;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Entity\Category;
use App\Modules\Product\Service\AttributeGroupService;
use App\Modules\Product\Service\AttributeService;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;

class AttributeController extends Controller
{

    private mixed $pagination;
    private AttributeService $service;
    private AttributeGroupService $groupService;


    public function __construct(AttributeService $service, AttributeGroupService $groupService)
    {
        $this->middleware(['auth:admin', 'can:commodity']);
        $this->pagination = Config::get('shop-config.p-list');
        $this->service = $service;
        $this->groupService = $groupService;
    }

    public function index(Request $request)
    {
        try {
            //TODO Перенести все в Repository!!!
            $query = Attribute::orderBy('name');
            $categories = Category::defaultOrder()->withDepth()->get();
            $groups = AttributeGroup::orderBy('name')->get();

            if (!empty($category_id = $request->get('category_id')) && $category_id != 0) {
                $query->whereHas('categories', function ($q) use ($category_id) {
                    $q->where('id', '=', $category_id);
                });
            }

            //if ($category_id == 0) unset($category_id);
            if (!empty($group_id = $request->get('group_id'))) {
                $query->where('group_id', $group_id);
            }

            //ПАГИНАЦИЯ
            if (!empty($pagination = $request->get('p'))) {
                $prod_attributes = $query->paginate($pagination);
                $prod_attributes->appends(['p' => $pagination]);
            } else {
                $prod_attributes = $query->paginate($this->pagination);
            }


            return view('admin.product.attribute.index',
                compact('prod_attributes',
                    'categories', 'groups', 'pagination', 'category_id', 'group_id'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }


    public function create()
    {
        try {
            $categories = Category::defaultOrder()->withDepth()->get();
            $groups = AttributeGroup::orderBy('name')->get();
            return view('admin.product.attribute.create', compact('categories', 'groups'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'group_id' => 'required|integer',
            'name' => 'required|string',
            'type' => 'required|integer',
        ]);
        try {
            $attribute = $this->service->create($request);
            return redirect()->route('admin.product.attribute.show', compact('attribute'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();

    }

    public function show(Attribute $attribute)
    {
        try {
            return view('admin.product.attribute.show', compact('attribute'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function edit(Attribute $attribute)
    {
        try {
            $categories = Category::defaultOrder()->withDepth()->get();
            $groups = AttributeGroup::orderBy('name')->get();
            return view('admin.product.attribute.edit', compact('attribute', 'categories', 'groups'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function update(Request $request, Attribute $attribute)
    {
        try {
            $attribute = $this->service->update($request, $attribute);
            return view('admin.product.attribute.show', compact('attribute'));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function destroy(Attribute $attribute)
    {
        try {
            $this->service->delete($attribute);
            return redirect()->route('admin.product.attribute.index');
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }


    //ГРУППЫ АТРИБУТОВ

    public function group_add(Request $request)
    {
        try {

            $this->groupService->create($request);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function groups(Request $request)
    {
        try {
            $groups = AttributeGroup::orderBy('sort')->get();
            return view('admin.product.attribute.groups', compact('groups'));
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function group_up(AttributeGroup $group)
    {
        try {
            $this->groupService->up($group);
            $groups = AttributeGroup::orderBy('sort')->get();
            return redirect(route('admin.product.attribute.groups', compact('groups')));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function group_down(AttributeGroup $group)
    {
        try {
            $this->groupService->down($group);
            $groups = AttributeGroup::orderBy('sort')->get();
            return redirect(route('admin.product.attribute.groups', compact('groups')));
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function group_rename(Request $request, AttributeGroup $group)
    {
        try {
            $this->groupService->update($request, $group);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    public function group_destroy(AttributeGroup $group)
    {
        try {
            $this->groupService->delete($group);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

    //Варианты

    public function variant_image(Request $request, AttributeVariant $variant)
    {
        try {
            $this->service->image_variant($variant, $request);
        } catch (\DomainException $e) {
            flash($e->getMessage(), 'danger');
        } catch (\Throwable $e) {
            event(new ThrowableHasAppeared($e));
            flash('Техническая ошибка! Информация направлена разработчику', 'danger');
        }
        return redirect()->back();
    }

}
