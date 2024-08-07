<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity;

use App\Modules\Admin\Entity\Options\Image;
use App\Modules\Admin\Entity\Options\Shop;
use Illuminate\Support\Facades\Config;
use JetBrains\PhpStorm\NoReturn;
use stdClass;

/**
 * @property int $id
 * @property string $shop_json
 * @property stdClass $shop
 */
class Options
{
    public Image $image;
    public mixed $report;

    public function __construct()
    {
        //Считываем из Config
        $image = Config::get('shop-config.image');
        $this->image = Image::createFromArray($image);
        $this->report = Config::get('shop-config.report');
    }

    /*
    public function __get($name)
    {
        $setting = Setting::where('slug', $name)->first();
        $object = new stdClass();
        $items = SettingItem::where('setting_id', '=', $setting->id)->get();
        /** @var SettingItem $item */
    /*
        foreach ($items as $item) {
            $key = $item->key;
            $object->$key = $item->value;
        }
        return $object;
    }*/
}
