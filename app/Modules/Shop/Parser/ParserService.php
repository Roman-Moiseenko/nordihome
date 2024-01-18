<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

use Illuminate\Http\Request;

class ParserService
{

    public function loadProduct()
    {

    }

    public function findProduct(Request $request)
    {

        //Ищем товар в базе
        //
        //Если находим, возвращаем Product
        //
        //Если нет
        //1. Добавляем черновик товара (Артикул, Главное фото, Название, Краткое описание, Базовая цена, published = false)
        //2. Возвращаем созданный Product
        //3. Создаем event -> Очередь на загрузку всех изображений, и Отправка уведомления на новый товар для Товароведа

        //Парсим цену и доступное кол-во,
        //Данные дополнительно сохраняем в таблице Товары поставщика

        //Возвращаем [Product, max_quantity, base_cost]

        return [];
    }
}
