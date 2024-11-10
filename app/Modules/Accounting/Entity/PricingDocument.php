<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Traits\CompletedFieldModel;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $number
 * @property boolean $completed
 * @property string $comment Комментарий к документу
 * @property int $arrival_id  - Основание
 * @property int $staff_id - автор документа
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property ArrivalDocument $arrival
 * @property PricingProduct[] $pricingProducts
 * @property Admin $staff
 */
class PricingDocument extends Model implements AccountingDocument
{
    use HtmlInfoData, CompletedFieldModel;

    protected $table = 'pricing_documents';
    protected $fillable = [
        'number',
        'completed',
        'comment',
        'arrival_id',
        'staff_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $staff_id, int $arrival_id = null):self
    {
        return self::create([
            'number' => null,
            'comment' => '',
            'arrival_id' => $arrival_id,
            'completed' => false,
            'staff_id' => $staff_id,
        ]);
    }

    public function isProduct(int $product_id): bool
    {
        foreach ($this->pricingProducts as $item) {
            if ($item->product_id == $product_id) return true;
        }
        return false;
    }

    public function setNumber()
    {
        $this->number = PricingDocument::where('number', '<>', null)->count() + 1;
        $this->save();
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    //***
    public function arrival()
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');
    }

    public function pricingProducts()
    {
        return $this->hasMany(PricingProduct::class, 'pricing_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
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
