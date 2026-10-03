<?php

namespace App\Modules\Accounting\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property float $ratio_weight
 * @property float $ratio_markup
 * @property int $rounding_step
 * @property int $rounding_subtract
 * @property bool $is_active
 */
class PricingRule extends Model
{
    protected $table = 'pricing_rules';

    protected $fillable = [
        'name',
        'ratio_weight',
        'ratio_markup',
        'rounding_step',
        'rounding_subtract',
        'is_active',
    ];

    protected $casts = [
        'ratio_weight' => 'float',
        'ratio_markup' => 'float',
        'rounding_step' => 'integer',
        'rounding_subtract' => 'integer',
        'is_active' => 'boolean',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(PricingRuleCategory::class, 'pricing_rule_id', 'id');
    }
}
