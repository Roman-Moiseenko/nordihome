<?php

namespace App\Modules\Marketplace\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $ad_id
 * @property string $avito_id
 * @property int $product_id
 * @property int $category_id
 * @property string $name
 * @property string $description
 * @property int $price
 * @property int $price_package
 * @property string $tn_ved
 * @property string $okpd2
 * @property bool $delivery
 * @property bool $active
 */
class AvitoProduct extends Model
{

}
