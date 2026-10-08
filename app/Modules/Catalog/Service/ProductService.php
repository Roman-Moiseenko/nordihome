<?php
declare(strict_types=1);

namespace App\Modules\Catalog\Service;

use App\Modules\Accounting\Entity\Distributor;
use App\Modules\Accounting\Entity\StorageItem;
use App\Modules\Accounting\Service\StorageService;
use App\Modules\Catalog\Infrastructure\Models\Brand;
use App\Modules\Catalog\Infrastructure\Models\Product;
use App\Modules\Setting\Entity\Common;
use App\Modules\Setting\Entity\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductService
{

    private StorageService $storageService;
    private Common $common_set;




    public function __construct(
        StorageService    $storageService,
        Settings          $settings,

    )
    {
        //Конфигурация


        $this->storageService = $storageService;
        $this->common_set = $settings->common;

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
            $product->push();
            $product->name_print = $request->string('name_print')->trim()->value();
            $product->comment = $request->string('comment')->trim()->value();
            $product->country_id = $request->input('country_id');

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



    public function published(Product $product): void
    {
        //TODO Проверка на заполнение и на модерацию - добавить другие проверки

        if ($product->getPriceRetail() == 0) {
            //Для товара не задана цена
            if (is_null($product->modification)/* && is_null($product->parser)*/) {
                //у товара нет модификации и нет товара из парсера
                throw new \DomainException('Для товара ' . $product->name . ' не задана цена');
            } else {
                if (is_null($product->modification->base_product->parser) &&
                    $product->modification->base_product->getPriceRetail() == 0
                ) {
                    //В базовом товаре модификации нет цены или нет парсера
                    throw new \DomainException('Для товара ' . $product->name . ' не задана цена');
                }
            }
        }


        $product->setPublished();
        if (!is_null($product->modification) && ($product->modification->base_product_id == $product->id)) {
            foreach ($product->modification->products as $_product) {
                $_product->setPublished();
            }
        }
    }





}
