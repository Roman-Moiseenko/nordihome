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




    public function uploadByXlsx($file, $brand_id): array
    {
        try {


            set_time_limit(100);
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file->getPathName());
            $sheetData = $spreadsheet->getActiveSheet()->toArray();
            $result = [];
            foreach ($sheetData as $row) {
                $result[] = array_values(array_filter($row, function ($item) {
                    return $item != null;
                }));
            }
            $array = array_values(array_filter($result));
            $brand = is_null($brand_id) ? null : Brand::find($brand_id);
            $products = [];
            foreach ($array as $item) {
                $products[] = [
                    'code' => $item[0],
                    'quantity' => isset($item[1]) ? (float)$item[1] : 1,
                    'price' => isset($item[2]) ? (float)str_replace(' ', '', $item[2]) : 0,
                    'price2' => isset($item[3]) ? (float)str_replace(' ', '', $item[3]) : 0,
                ];
            }

            set_time_limit(30);
            return ['products' => $products];
        } catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'products' => [],
            ];
        }
    }

    public function findParser(string $code, int $brand_id)
    {

        try {
            $brand = Brand::find($brand_id); //is_null($brand_id) ? null :
            // return $brand_id;
            if (is_null($product = Product::whereCode($code)->first()) && !is_null($brand)) {

                $parser_class = $brand->parser_class;
                $parser = app()->make($parser_class);
                $product = $parser->findProduct($code);
            }
            if (!is_null($product)) return $product->id;

            return 0;
        } catch (\Throwable $e) {
            return [
                'error' => $e->getMessage() . " " . $e->getLine() . " " . $e->getFile(),
            ];
        }
    }

    private function getFloatXls(null|string $item): float
    {
        if (is_null($item)) return 0;
        $item = str_replace(' ', '', $item);

        $item = preg_replace('/(\x{00a0}|\x{202f})/u', '', $item);

        $item = str_replace(',', '.', $item);
        return (float)$item;
        //\u202f
    }
}
