<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Entity\Photo;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeGroup;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\AttributeGroupRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeService
{
    private CategoryRepository $categories;
    private AttributeGroupRepository $groups;

    public function __construct(CategoryRepository $categories, AttributeGroupRepository $groups)
    {
        $this->categories = $categories;
        $this->groups = $groups;
    }

    public function create(Request $request): Attribute
    {
        DB::beginTransaction();
        try {
            $attribute = Attribute::register(
                $request->get('name'),
                (int)$request->get('group_id'),
                (int)$request->get('type')
            );

            foreach ($request->get('categories') as $category_id) {
                if ($this->categories->exists((int)$category_id))
                    $attribute->categories()->attach((int)$category_id);
            }

            if (isset($request['multiple'])) {
                $attribute->multiple = true;
            }
            if (isset($request['show-in'])) {
                $attribute->show_in = true;
            }
            if (isset($request['filter'])) {
                $attribute->filter = true;
            }
            $attribute->sameAs = $request['sameAs'] ?? '';
            $this->image($attribute, $request);

            if ($attribute->isVariant()) {
                foreach ($request['variants_value'] as $variant) {
                    if (!empty($variant)) $attribute->addVariant($variant);
                }
            }
            $attribute->push();
            DB::commit();
            return $attribute;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
    }

    public function update(Request $request, Attribute $attribute)
    {
        DB::beginTransaction();
        try {
            $attribute->name = $request->get('name');
            $attribute->group_id = (int)$request['group_id'];
            $attribute->multiple = !empty($request['multiple']);
            $attribute->filter = !empty($request['filter']);
            $attribute->show_in = !empty($request['show-in']);
            $attribute->type = (int)$request['type'];

            $attribute->sameAs = $request['sameAs'] ?? '';
            $this->image($attribute, $request);
            //Работа с категориями

            $array_old = [];
            $array_new = $request['categories'];

            foreach ($attribute->categories as $category) $array_old[] = $category->id;
            foreach ($array_old as $key => $item) {
                if (in_array($item, $array_new)) {
                    $key_new = array_search($item, $array_new);
                    unset($array_old[$key]);
                    unset($array_new[$key_new]);
                }
            }
            foreach ($array_old as $item){
                $attribute->categories()->detach((int)$item);
            }
            foreach($array_new as $item) {
                if ($this->categories->exists((int)$item)) {
                    $attribute->categories()->attach((int)$item);
                }
            }

            //Варианты

            foreach ($attribute->variants as $variant) {
                if (!in_array((string)$variant->id, $request['variants_id']))
                     AttributeVariant::destroy($variant->id);
            }
            if (!empty($request['variants_id']))
                foreach ($request['variants_id'] as $key => $item) {
                    if (empty($item) && !empty($request['variants_value'][$key])) {
                        $attribute->addVariant($request['variants_value'][$key]);
                    }
                }

            $attribute->push();
            $attribute->refresh();
            DB::commit();
            return $attribute;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
    }

    public function delete(Attribute $attribute)
    {
        //TODO Удаление связей с продуктами
        //$attribute->products()->detach();
        $attribute->categories()->detach();
        $attribute->delete();
    }

    public function image(Attribute $attribute, Request $request): void
    {
        if (!empty($attribute->image)) {
            $attribute->image->newUploadFile($request->file('file'));
        } else {
            $attribute->image()->save(Photo::upload($request->file('file')));
        }
        $attribute->refresh();
    }

    public function image_variant(AttributeVariant $variant, Request $request)
    {
        if (!empty($variant->image)) {
            $variant->image->newUploadFile($request->file('file'));
        } else {
            $variant->image()->save(Photo::upload($request->file('file')));
        }
        $variant->refresh();
    }
}
