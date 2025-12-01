<?php

namespace App\Modules\Page\Casts;

use App\Modules\Page\Classes\ListsForm;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ListsFormCasts implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return ListsForm::fromArray($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return json_encode($value->toArray());
    }
}
