<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;


class ProductService
{

    public function register(Request $request): Product
    {
        $product = Product::register($request['name'], $request['code'], $request['slug'] ?? '');

        //Установка Свойств из Product.

        //Загрузка Фото

        return $product;
    }

    public function published(Product $product): void
    {
        //TODO Проверка на заполнение
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

}
