<?php
declare(strict_types=1);

namespace App\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $created_at
 * @property string $action
 * @property string $url
 * @property string $request_params
 */
class LogActivity extends Model
{
    public $timestamps = false;
    protected $table = 'log_activities';

    protected $fillable = [
        'user_id',
        'created_at',
        'action',
        'request_params',
        'url',
    ];
    public static function register(int $user_id, $action, $url, array $request_params)
    {
        self::create([
            'user_id' => $user_id,
            'created_at' => now(),
            'action' => $action,
            'request_params' => json_encode($request_params),
            'url' => $url,
        ]);
    }
}
