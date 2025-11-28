<?php

namespace App\Modules\Feedback\Casts;

use App\Modules\Feedback\Classes\DataFieldFeedback;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class DataFieldFeedbackCasts implements CastsAttributes
{

    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return DataFieldFeedback::fromArray(json_decode($value, true));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return json_encode($value);
    }

}
