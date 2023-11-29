<?php
declare(strict_types=1);

namespace App\Modules\User\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property int $product_id
 * @property Carbon $created_at
 */
class Wish extends Model
{
    protected $table = 'wish';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'product_id',
        'created_at',
    ];

    //TODO Сделать избранное
}
