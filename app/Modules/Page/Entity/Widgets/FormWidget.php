<?php

namespace App\Modules\Page\Entity\Widgets;

use App\Modules\Page\Casts\ListsFormCasts;
use App\Modules\Page\Classes\ListsForm;

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
