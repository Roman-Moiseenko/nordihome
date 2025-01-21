<?php

namespace App\Modules\Parser\Entity;

use Illuminate\Database\Eloquent\Model;

class ProductParser extends Model
{

    public $timestamps = false;
    protected $table = 'parser_products';
}
