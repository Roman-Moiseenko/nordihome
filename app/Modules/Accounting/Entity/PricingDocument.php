<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Modules\Base\Traits\CompletedFieldModel;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JetBrains\PhpStorm\Deprecated;

/**
 * @property int $arrival_id  - Основание
 * @property ArrivalDocument $arrival
 * @property PricingProduct[] $pricingProducts
 */
class PricingDocument extends AccountingDocument
{
    protected string $blank = 'Установка цен';
    protected $table = 'pricing_documents';
    protected $fillable = [
        'arrival_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function register(int $staff_id, int $arrival_id = null):self
    {
        $pricing = parent::baseNew($staff_id);
        $pricing->arrival_id = $arrival_id;
        $pricing->save();
        return $pricing;
    }

    public function getManager(): string
    {
        if ($this->staff_id == null) return 'Не установлен';
        return $this->staff->fullname->getFullName();
    }

    public function arrival(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ArrivalDocument::class, 'arrival_id', 'id');
    }

    #[Deprecated]
    public function pricingProducts(): HasMany
    {
        return $this->hasMany(PricingProduct::class, 'pricing_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(PricingProduct::class, 'pricing_id', 'id');
    }

    function documentUrl(): string
    {
        return route('admin.accounting.pricing.show', ['pricing' => $this->id]);
    }

    public function onBased(): ?array
    {
        return [];
    }
}
