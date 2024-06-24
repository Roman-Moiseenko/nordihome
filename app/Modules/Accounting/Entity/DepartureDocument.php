<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Списание товаров
 * @property int $id
 * @property string $number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $completed
 * @property int $storage_id
 * @property string $comment Комментарий к документу, пока отключена, на будущее
 * @property int $staff_id - автор документа
 * @property Storage $storage
 * @property DepartureProduct[] $departureProducts
 * @property Admin $staff
 */
class DepartureDocument extends Model implements AccountingDocument
{
    use HtmlInfoData;

    protected $table = 'departure_documents';

    protected $fillable = [
        'storage_id',
        'number',
        'comment',
        'staff_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $storage_id, int $staff_id): self
    {
        return self::create([
            'number' => self::count() + 1,
            'storage_id' => $storage_id,
            'completed' => false,
            'staff_id' => $staff_id,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->completed == true;
    }

    public function isProduct(int $product_id): bool
    {
        foreach ($this->departureProducts as $item) {
            if ($item->product_id == $product_id) return true;
        }
        return false;
    }

    public function completed()
    {
        $this->completed = true;
        $this->save();
    }

    public function departureProducts()
    {
        return $this->hasMany(DepartureProduct::class, 'departure_id', 'id');
    }

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }

    #[ArrayShape([
        'quantity' => 'int',
        'cost' => 'float',
    ])]
    public function getInfoData(): array
    {
        $quantity = 0;
        $cost = 0;
        foreach ($this->departureProducts as $item) {
            $quantity += $item->quantity;
            $cost += $item->quantity * ($item->cost ?? 0);
        }
        return [
            'quantity' => $quantity,
            'cost' => $cost,
        ];
    }


    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
        $this->save();
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
