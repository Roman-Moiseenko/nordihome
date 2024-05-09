<?php
declare(strict_types=1);

namespace App\Modules\Admin\Entity;

use App\Casts\FullNameCast;
use App\Entity\FullName;
use App\Modules\Accounting\Entity\Storage;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property FullName $fullname
 * @property string $phone
 * @property int $post
 * @property bool $active
 * @property int $telegram_user_id
 * @property int $storage_id
 * @property Storage $storage
 */
class Worker extends Model
{

    public const DRIVER = 9701;
    public const LOADER = 9702;
    public const ASSEMBLE = 9703;

    public const POSTS = [
        self::DRIVER => 'Водитель',
        self::LOADER => 'Грузчик',
        self::ASSEMBLE => 'Сборщик',
    ];

    protected $attributes = [
        'fullname' => '{}',
        'storage_id' => null,
    ];
    protected $fillable = [
        'phone',
        'post',
        'active',
        'fullname',
    ];

    protected $casts = [
        'fullname' => FullNameCast::class
    ];

    public static function register(string $surname, string $firstname, string $secondname, int $post, string $phone): self
    {
        /** @var Worker $worker */
        $worker = self::make([
            'phone' => $phone,
            'post' => $post,
            'active' => true,
        ]);
        $worker->fullname->surname = $surname;
        $worker->fullname->firstname = $firstname;
        $worker->fullname->secondname = $secondname;
        $worker->save();
        return $worker;
    }

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

    public function isActive(): bool
    {
        return $this->active == true;
    }

    public function postHtml(): string
    {
        return self::POSTS[$this->post];
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id')->withDefault(['name' => '-']);
    }
}
