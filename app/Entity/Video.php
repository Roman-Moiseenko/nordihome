<?php
declare(strict_types=1);

namespace App\Entity;

use Illuminate\Database\Eloquent\Model;
/**
 * @property int $id
 * @property int $object_id
 * @property string $file
 * @property string $description
 * @property int $sort
 */
abstract class Video extends Model
{
    protected $fillable = [
        'video',
        'sort',
        'description',
    ];

    protected $hidden = [
        'product_id',
    ];

    final public static function register(string $file, int $object_id, string $description = '', int $sort = 0): self
    {
        return self::create([
            'object_id' => $object_id,
            'file' => $file,
            'description' => $description,
            'sort' => $sort,
        ]);
    }
}
