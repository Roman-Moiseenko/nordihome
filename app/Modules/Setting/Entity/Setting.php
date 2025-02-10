<?php
declare(strict_types=1);

namespace App\Modules\Setting\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property array $data
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $attributes = [
        'data' => '{}',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'data' => 'json',
    ];
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public static function register(string $name, string $slug, string $description): self
    {
        return Setting::create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
        ]);
    }

    public function setData($object): void
    {
        $this->data = $object;
        $this->save();
    }

    public function getData(): array
    {
        return $this->data;
    }
}
