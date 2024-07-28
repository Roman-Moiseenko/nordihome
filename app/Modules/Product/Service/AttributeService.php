<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Base\Entity\Photo;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\AttributeVariant;
use App\Modules\Product\Repository\AttributeGroupRepository;
use App\Modules\Product\Repository\CategoryRepository;
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
        DB::transaction(function () use ($request, &$attribute) {
            $attribute = Attribute::register(
                $request->get('name'),
                (int)$request->get('group_id'),
                (int)$request->get('type')
            );

            foreach ($request->get('categories') as $category_id) {
                if ($this->categories->exists((int)$category_id))
                    $attribute->categories()->attach((int)$category_id);
            }

            $attribute->multiple = $request->has('multiple');
            $attribute->show_in = $request->has('show-in');
            $attribute->filter = $request->has('filter');

            $attribute->sameAs = $request->string('sameAs')->trim()->value();
            $this->image($attribute, $request);

            if ($attribute->isVariant()) {
                foreach ($request['variants_value'] as $variant) {
                    if (!empty($variant)) $attribute->addVariant($variant);
                }
            }
            $attribute->push();
        });

        return $attribute;
    }

    public function update(Request $request, Attribute $attribute)
    {
        DB::transaction(function () use ($request, $attribute) {
            $attribute->name = $request->get('name');
            $attribute->group_id = $request->integer('group_id');
            $attribute->multiple = $request->has('multiple');
            $attribute->filter = $request->has('filter');
            $attribute->show_in = $request->has('show-in');
            $attribute->type = $request->integer('type');
            $attribute->sameAs = $request->string('sameAs')->trim()->value();
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
            foreach ($array_old as $item) {
                $attribute->categories()->detach((int)$item);
            }
            foreach ($array_new as $item) {
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
        });
    }

    public function delete(Attribute $attribute)
    {
        $attribute->categories()->detach();
        $attribute->delete();
    }

    public function image(Attribute $attribute, Request $request): void
    {
        if ($request->file('file') == null) return;
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
