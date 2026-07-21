<?php

namespace App\Modules\Feedback\Infrastructure\Models;

use App\Modules\Lead\Traits\LeadField;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


/**
 * @property int $id
 * @property string $url
 * @property string $form_name
 * @property Carbon $created_at
 * @property array $data - оригинальные данные с формы
 */
class FormBack extends Model
{

    use LeadField;

    public $timestamps = false;

    protected $table = 'feedback_forms';

    protected $attributes = [
        'data_form' => '{}',
    ];
    protected $fillable = [
        'form_name',
        'url',
        'created_at',
        'data',
    ];
    protected $casts = [
        'data_form' => 'json',
    ];




}
