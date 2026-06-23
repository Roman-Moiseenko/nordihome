<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Auth\Infrastructure\Models\Staff;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use function now;

/**
 * @property int $id
 * @property int $staff_id
 * @property Carbon $created_at
 * @property string $action
 * @property string $url
 * @property string $request_params
 * @property Staff $staff
 */
class LoggerActivity extends Model
{
    public $timestamps = false;
    protected $table = 'logger_activity';

    protected $fillable = [
        'staff_id',
        'created_at',
        'action',
        'request_params',
        'url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public static function register(int $user_id, $action, $url, array $request_params)
    {
        self::create([
            'staff_id' => $user_id,
            'created_at' => now(),
            'action' => $action,
            'request_params' => json_encode($request_params),
            'url' => $url,
        ]);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }
}
