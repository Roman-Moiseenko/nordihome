<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Admin\Entity\Options;
use App\Modules\Product\Entity\Product;
use Illuminate\Http\Request;


class ProductService
{

    private Options $options;

    public function __construct(Options $options)
    {
        //Конфигурация
        $this->options = $options;
    }

    public function register(Request $request): Product
    {
        /* SECTION 1*/
        $product = Product::register($request['name'], $request['code'], $request['slug'] ?? '');
        //Категории
        //Бренд

        /* SECTION 2*/
        //Описание, короткое описание, теги

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

        return $product;
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

}
