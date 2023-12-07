<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $organization_id
 * @property float $latitude
 * @property float $longitude
 * @property string $post //Почтовый индекс
 * @property string $address // заменить
 * @property bool $point_of_sale
 * @property bool $point_of_delivery
 * @property Organization $organization

 */
class Storage extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name',
        'slug',
        'organization_id',
        'latitude',
        'longitude',
        'post',
        'address',
        'point_of_sale',
        'point_of_delivery'
    ];

    public static function register(int $organization_id, string $name, bool $point_of_sale, string $slug = ''): self
    {
        return self::create([
            'organization_id' => $organization_id,
            'name' => $name,
            'slug' => empty($slug) ? Str::slug($name) : $slug,
            'point_of_sale' => $point_of_sale,
            'point_of_delivery' => false,
        ]);
    }

    public function setDelivery(string $post, string $address)
    {
        $this->update([
            'point_of_delivery' => true,
            'post' => $post,
            'address' => $address,
        ]);

    }

    public function setCoordinate(float $latitude, float $longitude)
    {
        $this->update([
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

}
