<?php
declare(strict_types=1);

namespace App\Modules\Shop\Parser;

class ParserCart
{
    /** @var ParserItem[] $items */
    public array $items;
    public int $delivery;
    public int $amount;
    
    public function __construct()
    {
        //TODO Загружаем корзину клиента по user_id или user_uuid
    }

    public function add($product)
    {
    }

    public function clear()
    {
    }
}
