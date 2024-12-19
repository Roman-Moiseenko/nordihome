<?php
declare(strict_types=1);

namespace App\Modules\Service\Entity;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use function class_basename;
use function storage_path;

/**
 * @property int $id
 * @property int $reportable_id
 * @property string $reportable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $file
 * @property string $name
 */
class Report extends Model
{

    protected $fillable = [
        'name',
        'file',
    ];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }


    //???
    private function base_path(): string
    {
        //TODO Настроить от класса
        return storage_path() . '/report/' . Str::slug(class_basename($this->reportable));
    }

    public static function boot()
    {
        parent::boot();

        self::saved(function (Report $report) {
            if (empty($report->name)) {
                $report->name = basename($report->file);
            }
        });
        self::deleting(function (Report $report) {

            $old_file = $report->file;
            if (is_file($old_file)) unlink($old_file);
        });
    }
}
