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
                $request->string('name')->trim()->value(),
                $request->integer('group_id'),
                $request->integer('type')
            );

            foreach ($request->input('categories') as $category_id) {
                if ($this->categories->exists((int)$category_id))
                    $attribute->categories()->attach((int)$category_id);
            }
            $attribute->push();
        });

        return $attribute;
    }

    public function setInfo(Request $request, Attribute $attribute): void
    {
        DB::transaction(function () use ($request, $attribute) {
            $attribute->name = $request->string('name')->trim()->value();
            $attribute->group_id = $request->integer('group_id');
            $attribute->multiple = $request->boolean('multiple');
            $attribute->filter = $request->boolean('filter');
            $attribute->show_in = $request->boolean('show-in');
            $attribute->type = $request->integer('type');
            $attribute->sameAs = $request->string('sameAs')->trim()->value();
            $attribute->save();
            $attribute->saveImage($request->file('file'), $request->boolean('clear_file'));

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
            $variants = $request->input('variants');
            if (is_null($variants)) return;

            //1. Удаляем отмененные
            $_ids = array_filter(array_map(function ($item) {
                if (!is_null($item['id'])) return (int)$item['id'];
                return false;
            }, $variants));
            foreach ($attribute->variants as $variant) {
                if (!in_array($variant->id, $_ids))
                    AttributeVariant::destroy($variant->id);
            }
            //2. Изменяем значения старых и добавляем новые
            foreach ($variants as $i => $item) {
                $file = $request->file('variants.'. $i.'.file');

                if (!is_null($item['id'])) { //2.1 Изменяем старые значения

                    $variant = AttributeVariant::find($item['id']);
                    $variant->name = $item['name'];
                    $variant->save();

                    $variant->saveImage($file, (bool)$item['clear_file']);
                } else { //2.2 Добавляем новые
                    $attribute->addVariant($item['name'], $file);
                }
            }
           // $attribute->push();
        });
    }

    public function delete(Attribute $attribute): void
    {
        $attribute->categories()->detach();
        $attribute->delete();
    }

    public function image(Attribute $attribute, Request $request): void
    {
        $attribute->saveImage($request->file('file'));

        /*
        if ($request->file('file') == null) return;
        if (!empty($attribute->image)) {
            $attribute->image->newUploadFile($request->file('file'));
        } else {
            $attribute->image()->save(Photo::upload($request->file('file')));
        }
        $attribute->refresh(); */
    }

    /*
    public function image_variant(AttributeVariant $variant, Request $request)
    {
        if (!empty($variant->image)) {
            $variant->image->newUploadFile($request->file('file'));
        } else {
            $variant->image()->save(Photo::upload($request->file('file')));
        }
        $variant->refresh();
    } */
}
