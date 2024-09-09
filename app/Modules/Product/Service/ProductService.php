<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Events\ParserPriceHasChange;
use App\Events\ProductHasBlocked;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Admin\Entity\Options;
use App\Modules\Base\Entity\Photo;
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
                'pre_order' => $this->options->shop->pre_order,
                'only_offline' => $this->options->shop->only_offline,
                'not_local' => !$this->options->shop->delivery_local,
                'not_delivery' => !$this->options->shop->delivery_all,
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

    public function destroy(Product $product)
    {
        if ($product->orderItems()->count()) {
            $product->setDraft(); //throw new \DomainException('Товар в заказах. Удалить нельзя');
        }
        $product->delete();
        //TODO При удалении, удалять все связанные файлы Фото и Видео

    }

    public function notSale(Product $product)
    {
        $product->not_sale = true;
        $product->save();

    }


    public function CheckNotSale(Product $product)
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
            $series = $this->seriesService->create($_series); //Создаем Серию
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
        $photo_id = $request->integer('photo_id');

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

        $photo_id = $request->integer('photo_id');
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
        $id = $request->integer('photo_id');
        $alt = $request->string('alt')->trim()->value();
        foreach ($product->photos as $photo) {
            if ($photo->id === $id) {
                $photo->update(['alt' => $alt]);
            }
        }
    }

    public function published(Product $product): void
    {
        //TODO Проверка на заполнение и на модерацию - добавить другие проверки
        if ($product->getPriceRetail() == 0) throw new \DomainException('Для товара ' . $product->name . ' не задана цена');

        if (is_null($product->photo)) {
            throw new \DomainException('Для товара ' . $product->name . ' нет главного фото');
        }
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

    //Приоритетные товары
    public function setPriorityProduct(int $product_id)
    {
        $product = Product::find($product_id);
        $product->setPriority(true);
    }

    public function setPriorityProducts(array $codes)
    {
        foreach ($codes as $code) {
            $product = Product::where('code', trim($code))->first();
            $product->setPriority(true);
        }
    }

    /**
     * Расчет цены для товаров Икеа (через Парсинг)
     * вызывать при изменении одно параметра: цена в Икеа, коэф.наценки, коэф-ты для товаров (хруп., санкцц.)
     */

    public function setCostProductIkea(int $product_id, string $founded, bool $event = true)
    {
        /** @var Product $product */
        $product = Product::find($product_id);
        if (is_null($product->parser)) {
            Log::info('товара Икеа не имеет запись в парсере ' . $product->code);
            return;
        }
        $bulk = ($product->parser->price * $this->parser_set->parser_coefficient +
            $product->dimensions->weight() * ($product->parser->isFragile() ? $this->parser_set->cost_weight_fragile : $this->parser_set->cost_weight)) *
            ($product->parser->isSanctioned() ? (1 + $this->parser_set->cost_sanctioned/100) : 1);
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

    public function updateCostAllProductsIkea()
    {
        $products = Product::where('published', true)->where('not_sale', false)->pluck('id')->toArray();
        foreach ($products as $product_id) {
            $this->setCostProductIkea($product_id, 'Изменение коэффициентов наценки',false);
        }
    }
}
