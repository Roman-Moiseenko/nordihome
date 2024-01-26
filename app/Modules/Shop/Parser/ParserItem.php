<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

use App\Modules\Product\Entity\Product;

class ParserItem
{

    public string $code; //Артикул
    public string $name; //Название
    public string $description; //Описание
    public string $link; //Ссылка на товар
    public string $image; //Изображение
    public float $weight; //Вес
    public array $quantity; //Кол-во на складе

    public int $count; //Кол-во на заказ
    public float $cost; //Цена
    public int $pack; //Кол-во в пачке
    public array $composite; //Список составных артикулов с кол-вом, для таблицы: xxx.xxx.xx - x шт.


    public function __construct()
    {

    }


}
