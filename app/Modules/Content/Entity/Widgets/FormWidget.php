<?php

namespace App\Modules\Content\Entity\Widgets;

use App\Modules\Content\Casts\ListsFormCasts;
use App\Modules\Content\Classes\ListsForm;

/**
 * @property array $fields
 * @property ListsForm $lists
 */
class FormWidget extends Widget
{

    protected $attributes = [
        'fields' => '[]',
        'lists' => '{}',
    ];

    public $fillable = [
        'fields',
    ];

    protected $casts = [
        'fields' => 'array',
        'lists' => ListsFormCasts::class,
    ];

    protected $table = "widget_forms";

    public function getFieldName(string $field): string
    {
        return isset($this->fields[$field]) ? $this->fields[$field] : $field;
    }
}
