<?php

namespace App\Modules\Notification\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Notification extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $fillable = [
        'name',
    ];

    public static function register(string $name): self
    {
        return self::create([
            'name' => $name,
        ]);
    }

    public function isField(): bool
    {
        /**
         * Ф-ции состояния
         */
        return true;
    }

    public function setField($value): void
    {
        /**
         * Сетеры
         */
        $this->field = $value;
        $this->save();
    }

    public function getField(): mixed
    {
        /**
         * Гетеры
         */
        return $this->field;
    }
}
