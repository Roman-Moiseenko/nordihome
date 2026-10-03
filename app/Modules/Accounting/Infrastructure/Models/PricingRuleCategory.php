<?php

namespace App\Modules\Accounting\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $category_id
 * @property int $pricing_rule_id
 */
class PricingRuleCategory extends Model
{
    public $timestamps = false;

    protected $table = 'pricing_rules_categories';

    protected $fillable = [
        'category_id',
        'pricing_rule_id',
    ];

    public function pricingRule(): BelongsTo
    {
        return $this->belongsTo(PricingRule::class, 'pricing_rule_id', 'id');
    }
}
