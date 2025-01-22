<?php

namespace App\Modules\Parser\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property
 */
class ProductParser extends Model
{
    public $timestamps = false;
    protected $table = 'parser_products';
}
