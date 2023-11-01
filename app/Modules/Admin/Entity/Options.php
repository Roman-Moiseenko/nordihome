<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity;



use App\Modules\Admin\Entity\Options\Image;
use App\Modules\Admin\Entity\Options\Shop;
use Illuminate\Support\Facades\Config;

/**
 * @property int $id
 * @property string $shop_json
 */
class Options
{
    public Image $image;
    public Shop $shop;

    public function __construct()
    {
        //Считываем из Config
        $image = Config::get('shop-config.image');
        $this->image = Image::createFromArray($image);

        $shop = Config::get('shop-config.shop');
        $this->shop = Shop::createFromArray($shop);

        //TODO сделать для считывания с БД
    }
}
