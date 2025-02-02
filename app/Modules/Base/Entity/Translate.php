<?php
declare(strict_types=1);

namespace App\Modules\Base\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $foreign
 * @property string $value
 */
class Translate extends Model
{
    protected $table = 'translates';
    public $timestamps = false;

    protected $fillable = [
        'foreign',
        'value'
    ];

    public static function register(string $foreign, string $value): self
    {
        return self::create([
            'foreign' => $foreign,
            'value' => $value,
        ]);
    }
}
