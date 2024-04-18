<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $number
 * @property boolean $completed
 * @property string $comment Комментарий к документу
 * @property int $arrival_id  - Основание
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ArrivalDocument $arrival
 * @property PricingProduct[] $pricingProducts
 */
class PricingDocument extends Model
{
    protected $table = 'pricing_documents';

    protected $fillable = [
        'number',
        'completed',
        'comment',
        'arrival_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(string $number, string $comment, int $arrival_id = null):self
    {
        return self::create([
            'number' => $number,
            'comment' => $comment,
            'arrival_id' => $arrival_id,
            'completed' => false,
        ]);
    }

    public function arrival()
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');
    }

    public function pricingProducts()
    {
        return $this->hasMany(PricingProduct::class, 'pricing_id', 'id');
    }
}
