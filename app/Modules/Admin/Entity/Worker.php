<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity;

use App\Modules\Accounting\Entity\Storage;
use App\Modules\Base\Casts\FullNameCast;
use App\Modules\Base\Entity\FullName;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property FullName $fullname
 * @property string $phone
 * @property bool $active
 * @property int $telegram_user_id
 * @property int $storage_id
 * @property bool $driver
 * @property bool $loader
 * @property bool $assemble
 * @property bool $logistic
 * @property Storage $storage
 */
class Worker extends Model
{
    protected $attributes = [
        'fullname' => '{}',
        'storage_id' => null,
    ];
    protected $fillable = [
        'phone',
        'active',
        'fullname',
    ];

    protected $casts = [
        'fullname' => FullNameCast::class
    ];

    public static function register(string $surname, string $firstname, string $secondname): self
    {
        /** @var Worker $worker */
        $worker = self::make([
            'active' => true,
        ]);
        $worker->fullname->surname = $surname;
        $worker->fullname->firstname = $firstname;
        $worker->fullname->secondname = $secondname;
        $worker->save();
        return $worker;
    }

    //*** IS-.....
    public function isActive(): bool
    {
        return $this->active == true;
    }

    public function isBlocked(): bool
    {
        return $this->active == false;
    }

    public function isDriver(): bool
    {
        return $this->driver == true;
    }
    public function isLoader(): bool
    {
        return $this->loader == true;
    }
    public function isAssemble(): bool
    {
        return $this->assemble == true;
    }

    public function isLogistic(): bool
    {
        return $this->logistic == true;
    }

    //*** SET-....
    public function setTelegram(int $id): void
    {
        $this->telegram_user_id = $id;
        $this->save();
    }

    public function setStorage(int $id): void
    {
        $this->storage_id = $id;
        $this->save();
    }

    public function activated()
    {
        $this->active = true;
        $this->save();
    }

    public function draft()
    {
        $this->active = false;
        $this->save();

    }

    //*** RELATIONS
    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id')->withDefault(['name' => '-']);
    }

    public function setPhone(string $value): void
    {
        $this->phone = preg_replace("/[^0-9]/", "", $value);
        $this->save();
    }
}
