<?php


namespace App\Entity;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Region
 * @package App\Entity
 * @property int $code
 * @property string $name
 */
class Region extends Model
{
    const DEFAULT_REGION = 39;
    protected $fillable = [
        'code',
        'name',
    ];
    public static function register(string $code, string $name): self
    {
        return static::create([
            'code' => $code,
            'name' => $name,
        ]);
    }
    public static function getByCode($code): self
    {
        $region = Region::where('code', $code)->first();
        if (empty($region)) {
            throw new \DomainException('Регион '. $code .' не найден');
        }
        return $region;
    }
}
