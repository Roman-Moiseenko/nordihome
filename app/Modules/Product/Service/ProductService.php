<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Events\ParserPriceHasChange;
use App\Events\ProductHasBlocked;
use App\Events\ProductHasFastCreate;
use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Admin\Entity\Options;
use App\Modules\Base\Entity\Dimensions;
use App\Modules\Base\Entity\Photo;
use App\Modules\Base\Entity\Video;
use App\Modules\Product\Entity\Attribute;
use App\Modules\Product\Entity\Bonus;
use App\Modules\Product\Entity\Composite;
use App\Modules\Product\Entity\Group;
use App\Modules\Product\Entity\Product;
use App\Modules\Product\Repository\CategoryRepository;
use App\Modules\Product\Repository\TagRepository;
use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Parser;
use App\Modules\Setting\Repository\SettingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Deprecated;

class ProductService
{
    private Options $options;
    private CategoryRepository $categories;
    private TagRepository $tags;
    private TagService $tagService;
    private EquivalentService $equivalentService;
    private SeriesService $seriesService;
    private StorageService $storageService;
    private Common $common_set;
    private Parser $parser_set;
    private GroupService $groupService;


    public function __construct(
        Options            $options,
        CategoryRepository $categories,
        TagRepository      $tags,
        TagService         $tagService,
        EquivalentService  $equivalentService,
        SeriesService      $seriesService,
        StorageService     $storageService,
        SettingRepository  $settings,
        GroupService       $groupService,
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

        $this->common_set = $settings->getCommon();
        $this->parser_set = $settings->getParser();
        $this->groupService = $groupService;
    }

    public function create(Request $request): Product
    {
        DB::transaction(function () use ($request, &$product) {
            $arguments = [
                'pre_order' => $this->common_set->pre_order,
                'local' => $this->common_set->delivery_local,
                'delivery' => $this->common_set->delivery_all,
            ];
            $product = Product::register(
                $request->string('name')->trim()->value(),
                $request->string('code')->trim()->value(),
                $request->integer('category_id'),
                $request->string('slug')->trim()->value(),
                $arguments);
            $product->brand_id = $request->integer('brand_id');
            if (!empty($request['categories'])) {
                foreach ($request['categories'] as $category_id) {
                    if ($this->categories->exists((int)$category_id))
                        $product->categories()->attach((int)$category_id);
                }
            }
            $this->series($request, $product);
            $product->push();
            $this->storageService->add_product($product);
        });

        return $product;
    }

    public function createFull(Request $request): Product
    {
        DB::transaction(function () use ($request, &$product) {
            $arguments = [
                'pre_order' => $this->common_set->pre_order,
                'local' => $this->common_set->delivery_local,
                'delivery' => $this->common_set->delivery_all,
            ];
            $product = Product::register(
                $request->string('name')->trim()->value(),
                $request->string('code')->trim()->value(),
                $request->integer('category_id'),
                $request->string('slug')->trim()->value(),
                $arguments);
            $product->brand_id = $request->integer('brand_id');
            if (!empty($request['categories'])) {
                foreach ($request['categories'] as $category_id) {
                    if ($this->categories->exists((int)$category_id))
                        $product->categories()->attach((int)$category_id);
                }
            }
            $product->push();
            $product->name_print = $request->string('name_print')->trim()->value();
            $product->comment = $request->string('comment')->trim()->value();
            $product->country_id = $request->input('country_id');
            $product->vat_id = $request->integer('vat_id');
            $product->measuring_id = $request->integer('measuring_id');
            $product->fractional = $request->boolean('fractional');
            $product->marking_type_id = $request->input('marking_type_id');
            if (($distributor_id = $request->integer('distributor_id')) > 0) {
                $distributor = Distributor::find($distributor_id);
                $distributor->addProduct($product, 0);
            }
            $product->save();

            $this->storageService->add_product($product);

        });

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

    public function create_fast(Request $request): Product
    {
        $product = $this->create($request);

        $product->pricesRetail()->create([
            'value' => $request->integer('price'),
            'founded' => 'Создано из заказа',
        ]);
        $product->pricesPre()->create([
            'value' => $request->integer('price'),
            'founded' => 'Создано из заказа',
        ]);

        event(new ProductHasFastCreate($product));
        return $product;
    }

    #[Deprecated]
    public function update(Request $request, Product $product): Product
    {

        /* SECTION 1*/
        //Основная
        /*
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
        */
        /* SECTION 2*/
        //Описание, короткое описание, теги
        /*
        $product->description = $request['description'] ?? '';
        $product->short = $request['short'] ?? '';
        $product->tags()->detach();
        $this->tags($request, $product);
*/
        /* SECTION 4*/
        //Видеообзоры
        /*
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
        */
        /* SECTION 5*/
        //Габариты и доставка
        /*
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
        */
        /* SECTION 6*/
        //Атрибуты
        /*
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
*/
        /* SECTION 7*/
        //Цена, кол-во, статус, периодичность
        /*
        if ($request->has('pre_order')) { //Если не компонент livewire
            $product->pre_order = isset($request['pre_order']);
            $product->only_offline = isset($request['offline']);

            $product->frequency = $request['frequency'] ?? Product::FREQUENCY_NOT;
        }*/
        /* SECTION 8*/
        /* SECTION 9*/
        //Аналоги
        /*
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
*/
        /* SECTION 10*/
        //Сопутствующие
        /*
        if ($request->has('related')) { //Если не компонент livewire
            $product->related()->detach();
            if (!empty($request['related'])) {
                foreach ($request['related'] as $related) {
                    if ($product->id != (int)$related) $product->related()->attach((int)$related);
                }
            }
        }
        */
        /* SECTION 13*/
        //Бонусный товар
        /*
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
        */
        //$product->push();
        /*
                if ($request->has('published')) { //Если не компонент livewire
                    if (isset($request['published'])) {
                        $this->published($product);
                    } else {
                        $this->draft($product);
                    }
                }
                */
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

    //УДАЛЕНИЕ ВОССТАНОВЛЕНИ
    public function destroy(Product $product): void
    {
        if ($product->orderItems()->count()) {
            $product->setDraft();
            throw new \DomainException('Товар в заказах. Удалить нельзя, перемещен в черновик');

        } else {
            foreach ($product->storageItems as $storageItem) {
                //Удаляем ячейки из Хранилищ
                $storageItem->delete();
            }
            $product->delete();
            flash('Товар удален. Возможно восстановление', 'success');
        }
        //TODO При удалении, удалять все связанные файлы Фото и Видео
    }

    public function full_delete(int $id): void
    {
        $product = Product::onlyTrashed()->where('id', $id)->first();
        $product->forceDelete();
        StorageItem::onlyTrashed()->where('product_id', $id)->forceDelete();
    }

    public function restore(int $id): void
    {
        $product = Product::onlyTrashed()->where('id', $id)->first();
        $product->restore();
        StorageItem::onlyTrashed()->where('product_id', $id)->restore();
    }

    public function notSale(Product $product): void
    {
        $product->not_sale = true;
        $product->save();
    }

    public function CheckNotSale(Product $product): void
    {
        if ($product->getQuantity() == 0 && $product->isSale()) {
            $product->setNotSale();
            event(new ProductHasBlocked($product));
            return;
        }

        if ($this->common_set->group_last_id > 0 && $product->getQuantity() != 0) {
            /** @var Group $group */
            $group = Group::find($this->common_set->group_last_id);
            $this->groupService->add_product($group, $product->id);
        }

    }

    public function editCommon(Product $product, Request $request): void
    {
        $update_attributes = false;
        $product->name = $request->string('name')->trim()->value();
        $product->code = $request->string('code')->trim()->value();
        if ($product->main_category_id != $request->integer('category_id')) {
            $product->main_category_id = $request->integer('category_id');
            $update_attributes = true;
        }
        $product->main_category_id = $request->integer('category_id');
        $product->slug = empty($request->string('slug')->value()) ? Str::slug($product->name) : $request->string('slug')->value();

        $product->brand_id = $request->integer('brand_id');
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
        if (!empty($array_old)) { //Список категорий, которые надо удалить
            $update_attributes = true;
            foreach ($array_old as $item) {
                $product->categories()->detach((int)$item);
            }
        }
        if (!is_null($array_new)) {//Список категорий, которые надо добавить
            $update_attributes = true;
            foreach ($array_new as $item) {
                if ($this->categories->exists((int)$item)) {
                    $product->categories()->attach((int)$item);
                }
            }
        }

        $product->name_print = $request->string('name_print')->trim()->value();
        $product->comment = $request->string('comment')->trim()->value();
        $product->country_id = $request->input('country_id');
        $product->vat_id = $request->integer('vat_id');
        $product->measuring_id = $request->integer('measuring_id');
        $product->fractional = $request->boolean('fractional');
        $product->marking_type_id = $request->input('marking_type_id');
        $product->save();

        //Проверка атрибутов, в случае смены категории
        if ($update_attributes) {
            $product->refresh();
            $array = array_map(function (Attribute $attribute) {
                return $attribute->id;
            }, $product->getPossibleAttribute());

            foreach ($product->prod_attributes as $attribute) {
                if (!in_array($attribute->id, $array)) {
                    $product->prod_attributes()->detach($attribute->id);
                }
            }
        }
    }

    public function editDescription(Product $product, Request $request): void
    {
        $product->description = $request->string('description')->trim()->value();
        $product->short = $request->string('short')->trim()->value();
        $product->model = $request->string('model')->trim()->value();
        $product->tags()->detach();
        $this->tags($request->input('tags'), $product);
        $this->series($request, $product);
        $product->save();
    }

    public function editDimensions(Product $product, Request $request): void
    {
        $product->dimensions = Dimensions::create(params: $request->input('dimensions'));
        $product->packages->clear();
        foreach ($request->input('packages') as $array) {
            $product->packages->create(params: $array);
        }
        $product->delivery = $request->boolean('delivery');
        $product->local = $request->boolean('local');
        $product->packages->complexity = $request->integer('complexity');

        $product->save();
    }

    public function editVideo(Product $product, Request $request): void
    {
        $product->videos()->delete();
        foreach ($request->input('videos') as $i => $item) {
            $product->videos()->save(Video::register(
                $item['url'],
                $item['caption'] ?? '',
                $item['description'] ?? '', $i));
            $product->save();
        }
    }

    public function editAttribute(Product $product, Request $request): void
    {
        DB::transaction(function () use ($product, $request) {
            $product->prod_attributes()->detach();
            foreach ($request->input('attributes') as $item) {
                $attribute = Attribute::find($item['id']);

                $value = null;
                if (!isset($item['value'])) {
                    if ($attribute->isBool()) $value = false;
                    if ($attribute->isNumeric()) $value = 0;
                    if ($attribute->isString()) $value = '';
                } else {
                    if ($attribute->isNumeric()) {
                        $value = (float)$item['value'];
                    } else {
                        $value = $item['value'];
                    }
                }
                $product->prod_attributes()->attach($attribute->id, ['value' => json_encode($value)]);
            }
            $product->save();
        });
    }

    public function editManagement(Product $product, Request $request): void
    {
        if ($request->boolean('published')) {
            $product->setPublished();
        } else {
            $product->setDraft();
        }
        if ($request->boolean('not_sale')) {
            $product->setNotSale();
        } else {
            $product->setForSale();
        }
        $product->priority = $request->boolean('priority');
        $product->hide_price = $request->boolean('hide_price');
        $product->frequency = $request->integer('frequency');
        $product->save();

        $product->balance->min = $request->integer('balance.min');
        $product->balance->max = $request->input('balance.max');
        $product->balance->buy = $request->boolean('balance.buy');
        $product->push();

        foreach ($request->input('storages') as $item) {
            $storageItem = StorageItem::find($item['id']);
            $storageItem->cell = $item['cell'];
            $storageItem->save();
        }
    }

    public function editEquivalent(Product $product, Request $request): void
    {
        $equivalent_id = $request->integer('equivalent_id');

        if ($equivalent_id == 0 && !is_null($product->equivalent)) {
            $this->equivalentService->delProductByIds($product->equivalent->id, $product->id);
        }
        if ($equivalent_id != 0) {
            if (is_null($product->equivalent)) {
                $this->equivalentService->addProductByIds($equivalent_id, $product->id);
            } elseif ($equivalent_id !== $product->equivalent->id) {
                $this->equivalentService->delProductByIds($product->equivalent->id, $product->id);
                $this->equivalentService->addProductByIds($equivalent_id, $product->id);
            }
        }
        $product->save();
    }

    public function editRelated(Product $product, Request $request): void
    {
        $product_id = $request->integer('product_id');
        if ($request->string('action')->value() == 'remove') {
            $product->related()->detach($product_id);
        } else {
            if ($product->isRelated($product_id)) throw new \DomainException('Товар уже добавлен в Аксессуары');
            $product->related()->attach($product_id);
        }
        $product->save();
    }

    public function editBonus(Product $product, Request $request): void
    {
        $product_id = $request->integer('product_id');
        $action = $request->string('action')->value();
        if (empty($action)) {
            if ($product->id === $product_id) throw new \DomainException('Товар совпадает с текущим');
            if ($product->isBonus($product_id)) throw new \DomainException('Товар уже добавлен в Бонусные');
            $bonus = Bonus::where('bonus_id', $product_id)->first();
            if (!is_null($bonus)) throw new \DomainException('Товар уже назначен бонусным у товара ' . $bonus->product->name);
            $bonus_add = Product::find($product_id);
            $product->bonus()->attach($product_id, ['discount' => $bonus_add->getPriceRetail()]);
        }
        if ($action == 'remove') {
            $product->bonus()->detach($product_id);
        }
        if ($action == 'edit') {
            foreach ($request->input('bonus') as $item) {
                $product->bonus()->updateExistingPivot($item['id'], ['discount' => (int)$item['discount']]);
            }
        }
    }

    public function editComposite(Product $product, Request $request): void
    {
        $product_id = $request->integer('product_id');
        $action = $request->string('action')->value();


        if (empty($action)) {
            if ($product->id == $product_id) throw new \DomainException('Товар совпадает с текущим');
            if ($product->isComposite($product_id)) throw new \DomainException('Товар уже добавлен в Составной');

            if (!is_null(Composite::where('child_id', $product->id)->first()))
                throw new \DomainException('Текущий товар уже является составным');
            $quantity = $request->integer('quantity');
            $product->composites()->attach($product_id, ['quantity' => $quantity]);
        }
        if ($action == 'remove') {
            $product->composites()->detach($product_id);
        }
        if ($action == 'edit') {
            foreach ($request->input('composite') as $item) {
                $product->composites()->updateExistingPivot($item['id'], ['quantity' => $item['quantity']]);
            }
        }
    }

    private function tags($tags, Product &$product): void
    {
        if (empty($tags)) return;
        foreach ($tags as $index => $tag_id) {
            if ($this->tags->exists($tag_id)) {
                $product->tags()->attach((int)$tag_id);
            } else {
                $tag = $this->tagService->create($tag_id);
                $product->tags()->attach($tag->id);
            }
        }
    }

    private function series(Request $request, Product &$product): void
    {
        if (empty($_series = $request['series_id'])) return;
        if (is_array($_series)) $_series = $_series[0]; //Если массив, берем первый элемент
        if (is_numeric($_series)) {
            $product->series_id = (int)$_series;
        } else {
            $series = $this->seriesService->create($_series); //Создаем Серию
            $product->series_id = $series->id;
        }
    }

    ///Работа с Фото Продукта
    public function addPhoto(Request $request, Product $product): Photo
    {
        return $product->addImage($request->file('file'));
        /*
        if (empty($file = $request->file('file'))) throw new \DomainException('Нет файла');
        $sort = count($product->photos);
        $photo = Photo::upload($file, '', $sort);
        $product->photo()->save($photo);
        $photo->refresh();
        return $photo; */
    }

    public function delPhoto(Request $request, Product $product): void
    {
        //return;
        $product->delImage($request->integer('photo_id'));
        /*
        $photo = Photo::find($request->integer('photo_id'));
        $photo->delete();
        foreach ($product->photos as $i => $photo) {
            $photo->update(['sort' => $i]);
        }*/
    }

    #[Deprecated]
    public function upPhoto(int $photo_id, Product $product): void
    {
        $product->upImage($photo_id);
    }

    #[Deprecated]
    public function downPhoto(int $photo_id, Product $product): void
    {
        $product->downImage($photo_id);
    }

    public function movePhoto(Request $request, Product $product): void
    {
        $new_sort = $request->input('new_sort');

        foreach ($new_sort as $i => $id) {
            $photo = Photo::find($id);
            $photo->sort = $i;
            $photo->save();
        }
    }

    public function setPhoto(Request $request, Product $product): void
    {
        $id = $request->integer('photo_id');
        $product->setAlt(photo_id: $id,
            alt: $request->string('alt')->trim()->value(),
            title: $request->string('title')->trim()->value(),
            description: $request->string('description')->trim()->value(),
        );
      /*  foreach ($product->gallery as $photo) {
            if ($photo->id === $id) {
                $photo->update([

                ]);
            }
        }*/
    }

    public function published(Product $product): void
    {
        //TODO Проверка на заполнение и на модерацию - добавить другие проверки
        if ($product->getPriceRetail() == 0) throw new \DomainException('Для товара ' . $product->name . ' не задана цена');

        if ($product->gallery()->count() == 0) {
            throw new \DomainException('Для товара ' . $product->name . ' нет изображений');
        }
        $product->setPublished();
    }

    public function draft(Product $product): void
    {
        $product->setDraft();
    }

    public function action(string $action, array $ids): void
    {
        if (empty($ids)) throw new \DomainException('Не выбраны товары');
        if (empty($action)) throw new \DomainException('Не выбрано действие');
        foreach ($ids as $product_id) {
            /** @var Product $product */
            $product = Product::find($product_id);
            if ($action == 'draft' && $product->isPublished()) $product->setDraft();
            if ($action == 'published' && !$product->isPublished()) $product->setPublished();

            if ($action == 'not_sale' && $product->isSale()) $product->setNotSale();
            if ($action == 'to_sale' && !$product->isSale()) $product->setForSale();

            if ($action == 'remove') $this->destroy($product);
        }
    }

    /**
     * Расчет цены для товаров Икеа (через Парсинг)
     * вызывать при изменении одно параметра: цена в Икеа, коэф.наценки, коэф-ты для товаров (хруп., санкцц.)
     */

    public function setCostProductIkea(int $product_id, string $founded, bool $event = true): void
    {
        /** @var Product $product */
        $product = Product::find($product_id);
        if (is_null($product->parser)) {
            Log::info('товара Икеа не имеет запись в парсере ' . $product->code);
            return;
        }
        $bulk = ($product->parser->price * $this->parser_set->parser_coefficient +
                $product->weight() * ($product->parser->isFragile() ? $this->parser_set->cost_weight_fragile : $this->parser_set->cost_weight)) *
            ($product->parser->isSanctioned() ? (1 + $this->parser_set->cost_sanctioned / 100) : 1);

        $retail = ceil($bulk * (1 + $this->parser_set->cost_retail / 100));
        $retail = (int)ceil($retail / 100) * 100 - 10;

        $pre = $product->parser->price * $this->parser_set->parser_coefficient;
        $min = (int)($retail / 2);

        if ($product->getPriceBulk() != $bulk)
            $product->pricesBulk()->create(['value' => $bulk, 'founded' => $founded]);

        if ($product->getPriceRetail() != $retail) {
            $product->pricesRetail()->create(['value' => $retail, 'founded' => $founded]);
            $product->pricesMin()->create(['value' => $min, 'founded' => $founded]);
        }
        if ($product->getPricePre() != $pre)
            $product->pricesPre()->create(['value' => $pre, 'founded' => $founded]);

        if ($event) event(new ParserPriceHasChange($product->parser));
    }

    public function updateCostAllProductsIkea(): void
    {
        $products = Product::where('published', true)->where('not_sale', false)->pluck('id')->toArray();
        foreach ($products as $product_id) {
            $this->setCostProductIkea($product_id, 'Изменение коэффициентов наценки', false);
        }
    }

}
