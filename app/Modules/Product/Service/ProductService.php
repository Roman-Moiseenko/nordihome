<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Entity\Photo;
use App\Entity\Video;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Admin\Entity\Options;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Product\Entity\Bonus;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ProductService
{

    private Options $options;
    private CategoryRepository $categories;
    private TagRepository $tags;
    private TagService $tagService;
    private EquivalentService $equivalentService;
    private SeriesService $seriesService;
    private StorageService $storageService;

    public function __construct(Options            $options,
                                CategoryRepository $categories,
                                TagRepository      $tags,
                                TagService         $tagService,
                                EquivalentService  $equivalentService,
                                SeriesService      $seriesService,
                                StorageService     $storageService,
    )
    {
        //Конфигурация
        $this->options = $options;
        $this->categories = $categories;
        $this->tags = $tags;
        $this->tagService = $tagService;
        $this->equivalentService = $equivalentService;
        $this->seriesService = $seriesService;
        $this->storageService = $storageService;
    }

    public function create(Request $request): Product
    {
        //TODO Переделать под получения массива $request->all()

        /* SECTION 1*/
        $arguments = [
            'pre_order' => $this->options->shop->pre_order,
            'only_offline' => $this->options->shop->only_offline,
            'not_local' => !$this->options->shop->delivery_local,
            'not_delivery' => !$this->options->shop->delivery_all,
        ];
        $product = Product::register($request['name'], $request['code'], (int)$request['category_id'], $request['slug'] ?? '', $arguments);
        $product->brand_id = $request['brand_id'];
        if (!empty($request['categories'])) {
            foreach ($request['categories'] as $category_id) {
                if ($this->categories->exists((int)$category_id))
                    $product->categories()->attach((int)$category_id);
            }
        }
        //Серия
        $this->series($request, $product);

        /* SECTION 2*/
        //Описание, короткое описание, теги
        $product->description = $request['description'] ?? '';
        $product->short = $request['short'] ?? '';

        $this->tags($request, $product);

        $product->push();
        $this->storageService->add_product($product);
        return $product;

    }

    public function create_parser(string $name, string $code, int $category_id, array $arguments): Product
    {
        $product = Product::register(
            $name,
            $code,
            $category_id,
            '',
            $arguments
        );
        $this->storageService->add_product($product);
        return $product;
    }

    public function update(Request $request, Product $product): Product
    {
        //TODO Удалить блоки которые через Livewire

        /* SECTION 1*/
        //Основная
        $product->name = $request['name'];
        $product->code = $request['code'];
        $product->slug = empty($request['slug']) ? Str::slug($request['name']) : $request['slug'];
        $product->main_category_id = $request['category_id'];
        $product->brand_id = $request['brand_id'];

        //Проверить изменения в списке категорий
        $array_old = [];
        $array_new = $request['categories'] ?? null;

        foreach ($product->categories as $category) $array_old[] = $category->id;
        foreach ($array_old as $key => $item) {
            if (!is_null($array_new) && in_array($item, $array_new)) {
                $key_new = array_search($item, $array_new);
                unset($array_old[$key]);
                unset($array_new[$key_new]);
            }
        }
        foreach ($array_old as $item) {
            $product->categories()->detach((int)$item);
        }
        if (!is_null($array_new)) {
            foreach ($array_new as $item) {
                if ($this->categories->exists((int)$item)) {
                    $product->categories()->attach((int)$item);
                }
            }
        }
        $this->series($request, $product);
        /* SECTION 2*/
        //Описание, короткое описание, теги
        $product->description = $request['description'] ?? '';
        $product->short = $request['short'] ?? '';
        $product->tags()->detach();
        $this->tags($request, $product);

        /* SECTION 4*/
        //Видеообзоры
        $product->videos()->delete();
        if (!empty($request['video_url'])) {
            foreach ($request['video_url'] as $i => $item) {
                if (!empty($request['video_url'][$i]))
                    $product->videos()->save(Video::register(
                        $request['video_url'][$i],
                        $request['video_caption'][$i] ?? '',
                        $request['video_text'][$i] ?? '', $i));
            }
        }
        /* SECTION 5*/
        //Габариты и доставка
        if ($request->has('dimensions-measure')) { //Если не компонент livewire
            $product->dimensions = Dimensions::create(
                (float)$request['dimensions-width'],
                (float)$request['dimensions-height'],
                (float)$request['dimensions-depth'] ?? 0,
                (float)$request['dimensions-weight'],
                $request['dimensions-measure'],
                (int)$request['dimensions-type']
            );
            $product->not_local = !isset($request['local']);
            $product->not_delivery = !isset($request['delivery']);
        }
        /* SECTION 6*/
        //Атрибуты
        $product->prod_attributes()->detach();
        foreach ($product->getPossibleAttribute() as $key => $attribute) {
            if (isset($request['attribute_' . $key])) {
                if ($attribute->isVariant()) {
                    $value = $request['attribute_' . $key];
                } elseif ($attribute->isBool()) {
                    $value = true;
                } else {
                    $value = $request['attribute_' . $key];
                }
                $product->prod_attributes()->attach($attribute->id, ['value' => json_encode($value)]);
            }
        }

        /* SECTION 7*/
        //Цена, кол-во, статус, периодичность
        if ($request->has('pre_order')) { //Если не компонент livewire
            $product->pre_order = isset($request['pre_order']);
            $product->only_offline = isset($request['offline']);

            $product->frequency = $request['frequency'] ?? Product::FREQUENCY_NOT;
        }
        /* SECTION 8*/

        /* SECTION 9*/
        //Аналоги
        $new_equivalent_id = $request['equivalent_id'] ?? 0;

        if ($new_equivalent_id == 0 && !is_null($product->equivalent)) {
            $this->equivalentService->delProductByIds($product->equivalent->id, $product->id);
        }
        if ($new_equivalent_id != 0) {
            if (is_null($product->equivalent)) {
                //Доб.новый
                $this->equivalentService->addProductByIds((int)$new_equivalent_id, $product->id);
            } elseif ((int)$new_equivalent_id !== $product->equivalent->id) {
                $this->equivalentService->delProductByIds($product->equivalent->id, $product->id);
                $this->equivalentService->addProductByIds((int)$new_equivalent_id, $product->id);
            }
        }

        /* SECTION 10*/
        //Сопутствующие
        if ($request->has('related')) { //Если не компонент livewire
            $product->related()->detach();
            if (!empty($request['related'])) {
                foreach ($request['related'] as $related) {
                    if ($product->id != (int)$related) $product->related()->attach((int)$related);
                }
            }
        }
        /* SECTION 13*/
        //Бонусный товар
        if ($request->has('bonus')) { //Если не компонент livewire
            $product->bonus()->detach();
            if (!empty($request['bonus'])) {
                foreach ($request['bonus'] as $key => $bonus) {
                    if ($product->id != (int)$bonus) {
                        $product->bonus()->attach((int)$bonus, ['discount' => (int)$request['discount'][$key]]);
                    }
                }
            }
        }
        $product->push();

        if ($request->has('published')) { //Если не компонент livewire
            if (isset($request['published'])) {
                $this->published($product);
            } else {
                $this->draft($product);
            }
        }
        return $product;
    }


    public function moderation(Product $product): void
    {
        //TODO Проверка на заполнение
       // $product->setModeration();
    }

    public function approved(Product $product): void
    {
        //TODO Проверка на заполнение
        //$product->setApproved();
    }

    public function destroy(Product $product)
    {
        if ($product->orderItems()->count()) {
            $product->setDraft(); //throw new \DomainException('Товар в заказах. Удалить нельзя');
        }
        $product->delete();
        //TODO При удалении, удалять все связанные файлы Фото и Видео

    }

    private function tags(Request $request, Product &$product)
    {
        if (empty($request['tags'])) return;
        foreach ($request->get('tags') as $tag_id) {
            if ($this->tags->exists($tag_id)) {
                $product->tags()->attach((int)$tag_id);
            } else {
                $tag = $this->tagService->create($tag_id);
                $product->tags()->attach($tag->id);
            }
        }
    }

    private function series(Request $request, Product &$product)
    {
        if (empty($_series = $request['series_id'])) return;
        if (is_array($_series)) $_series = $_series[0]; //Если массив, берем первый элемент
        if (is_numeric($_series)) {
            $product->series_id = (int)$_series;
        } else {
            $series = $this->seriesService->registerName($_series); //Создаем Серию
            $product->series_id = $series->id;
        }
    }

    ///Работа с Фото Продукта
    public function addPhoto(Request $request, Product $product)
    {
        if (empty($file = $request->file('file'))) throw new \DomainException('Нет файла');
        $sort = count($product->photos);
        $product->photo()->save(Photo::upload($file, '', $sort));
    }

    public function delPhoto(Request $request, Product $product)
    {
        $photo = Photo::find($request['photo_id']);
        $photo->delete();
        foreach ($product->photos as $i => $photo) {
            $photo->update(['sort' => $i]);
        }
    }

    public function upPhoto(Request $request, Product $product)
    {
        $photo_id = (int)$request['photo_id'];

        $photos = [];
        foreach ($product->photos as $i => $photo) {
            $photos[] = $photo;
        }

        for ($i = 1; $i < count($photos); $i++) {
            if ($photos[$i]->id == $photo_id) {
                $prev = $photos[$i - 1]->sort;
                $next = $photos[$i]->sort;
                $photos[$i]->update(['sort' => $prev]);
                $photos[$i - 1]->update(['sort' => $next]);
            }
        }
    }

    public function downPhoto(Request $request, Product $product)
    {
        /** @var Photo[] $photos */
        $photos = [];
        foreach ($product->photos as $i => $photo) {
            $photos[] = $photo;
        }

        $photo_id = (int)$request['photo_id'];
        for ($i = 0; $i < count($photos) - 1; $i++) {
            if ($photos[$i]->id == $photo_id) {
                $prev = $photos[$i + 1]->sort;
                $next = $photos[$i]->sort;
                $photos[$i]->update(['sort' => $prev]);
                $photos[$i + 1]->update(['sort' => $next]);
            }
        }
    }

    public function altPhoto(Request $request, Product $product)
    {
        $id = $request['photo_id'];
        $alt = $request['alt'];
        foreach ($product->photos as $photo) {
            if ($photo->id === (int)$id) {
                $photo->update(['alt' => $alt]);
            }
        }
    }


    public function published(Product $product): void
    {
        //TODO Проверка на заполнение и на модерацию - добавить другие проверки
        if ($product->getLastPrice() == 0) throw new \DomainException('Для товара ' . $product->name . ' не задана цена');
        $product->setPublished();
    }

    public function draft(Product $product): void
    {
        $product->setDraft();
    }

    public function action(string $action, array $ids)
    {
        foreach ($ids as $product_id) {
            /** @var Product $product */
            $product = Product::find($product_id);

            if ($action == 'draft' && $product->isPublished()) $product->setDraft();
            if ($action == 'published' && !$product->isPublished()) $product->setPublished();

            if ($action == 'remove') $this->destroy($product);
        }

    }
}
