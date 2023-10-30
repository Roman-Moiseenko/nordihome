<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductService
{

    private Options $options;
    private CategoryRepository $categories;
    private TagRepository $tags;
    private TagService $tagService;

    public function __construct(Options $options, CategoryRepository $categories, TagRepository $tags, TagService $tagService)
    {
        //Конфигурация
        $this->options = $options;
        $this->categories = $categories;
        $this->tags = $tags;
        $this->tagService = $tagService;
    }

    public function create(Request $request): Product
    {
        DB::beginTransaction();
        try {


            /* SECTION 1*/
            $product = Product::register($request['name'], $request['code'], (int)$request['category_id'], $request['slug'] ?? '');
            $product->brand_id = $request['brand'];
            //$product->main_category_id = $request['category_id'];
            foreach ($request->get('categories') as $category_id) {
                if ($this->categories->exists((int)$category_id))
                    $product->categories()->attach((int)$category_id);
            }
            /* SECTION 2*/
            //Описание, короткое описание, теги
            $product->description = $request['description'];
            $product->short = $request['short'];
            foreach ($request->get('tags') as $tag_id) {
                if ($this->tags->exists($tag_id)) {
                    $product->tags()->attach((int)$tag_id);
                } else {
                    $tag = $this->tagService->create(['name' => $tag_id]);
                    $product->tags()->attach($tag->id);
                }
            }

            /* SECTION 3*/
            //Изображения, главное

            /* SECTION 4*/
            //Видеообзоры

            /* SECTION 5*/
            //Габариты и доставка

            /* SECTION 6*/
            //Атрибуты

            /* SECTION 7*/
            //Цена, кол-во, статус, периодичность

            /* SECTION 8*/
            //Модификации - только в режиме update

            /* SECTION 9*/
            //Аналоги

            /* SECTION 10*/
            //Сопутствующие


            /* SECTION 11*/
            //Опции

            /* SECTION 13*/
            //Бонусный товар
            DB::commit();
            $product->push();
            return $product;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw new \DomainException($e->getMessage());
        }
    }

    public function published(Product $product): void
    {
        //TODO Проверка на заполнение и на модерацияю
        $product->setPublished();
    }


    public function moderation(Product $product): void
    {
        //TODO Проверка на заполнение
        $product->setModeration();
    }

    public function approved(Product $product): void
    {
        //TODO Проверка на заполнение
        $product->setApproved();
    }


    public function destroy()
    {
        //TODO Проверка на продажи и Отзывы- через сервисы reviewService->isSet($product->id) reviewOrder->isSet($product->id)
        //TODO При удалении, удалять все связанные файлы Фото и Видео
    }

    public function update(Request $request, Product $product): Product
    {
        return $product;
    }


}
