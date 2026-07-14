<?php

namespace App\Modules\Content\Infrastructure\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $category
 * @property array $schema
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Widget extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'category', 'schema'];
    protected $casts = [
        'schema' => 'array',
    ];
    public function instances()
    {
        return $this->hasMany(WidgetInstance::class);
    }
}
