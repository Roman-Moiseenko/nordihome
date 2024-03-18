<?php
declare(strict_types=1);

namespace App\Modules\Analytics\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $logger_id
 * @property string $object
 * @property string $action
 * @property string $value
 */
class LoggerCronItem extends Model
{
    public $timestamps = false;
    protected $table = 'logger_cron_items';
    protected $fillable = [
        'object',
        'action',
        'value',
    ];

}
