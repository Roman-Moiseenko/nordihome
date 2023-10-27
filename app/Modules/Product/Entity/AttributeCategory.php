<?php
declare(strict_types=1);

namespace App\Modules\Product\Entity;

use Illuminate\Database\Eloquent\Model;

class AttributeCategory extends Model
{

    public $timestamps = false;
    protected $table = 'attributes_categories';
}
