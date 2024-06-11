<?php
declare(strict_types=1);

namespace App\Modules\Base\Casts;

use App\Modules\Base\Entity\Dimensions;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class DimensionsCast implements CastsAttributes
{

    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return Dimensions::fromArray(json_decode($value, true));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return json_encode($value->toArray());
    }

}
