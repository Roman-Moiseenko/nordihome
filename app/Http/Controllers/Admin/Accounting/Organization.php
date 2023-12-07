<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property Storage[] $storages
 */
class Organization extends Model
{
    protected $fillable = [
        'name',
        'short_name',
    ];
    public $timestamps = false;

    public static function register(string $name, string $short_name = ''): self
    {
        return self::create([
            'name' => $name,
            'short_name' => $short_name,
        ]);
    }

    public function storages()
    {
        return $this->hasMany(Storage::class, 'organization_id', 'id');
    }
}
