<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 *@property int $id
 *@property int $product_id
 *@property string $name
 *@property Product[] $products
 */
class Series extends Model
{

    public $timestamps = false;
    protected $table = 'series';



}
