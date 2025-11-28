<?php

namespace App\Modules\Page\Entity;


use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

/**
 * @property int $id
 * @property bool $active
 * @property string $name
 * @property string $template
 * @property string $caption
 * @property string $description
 * @property WidgetItem[] $items
 */
abstract class Widget extends Model
{
    use ImageField, IconField;

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $casts = [
        ];
        $fillable = [
            'name',
            'active',
            'template',
            'caption',
            'description',
        ];
        $attributes = [

        ];

        $this->casts = array_merge($this->casts, $casts);
        $this->fillable = array_merge($this->fillable, $fillable);
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public static function register(string $name, string $template): static
    {
        return self::create([
            'name' => $name,
            'active' => false,
            'template' => $template,

        ]);
    }

    public function isActive(): bool
    {
        return $this->active == true;
    }

    //Для создания таблиц
    final public static function columns(Blueprint $table): void
    {
        $table->boolean('active')->default(false);
        $table->string('name');
        $table->string('template');
        $table->string('caption')->default('');
        $table->string('description')->default('');
    }
}
