<?php

namespace App\Modules\Lead\Entity;

use App\Modules\Feedback\Casts\DataFieldFeedbackCasts;
use App\Modules\Feedback\Classes\DataFieldFeedback;
use App\Modules\Feedback\Entity\FormBack;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @property int $id
 * @property int $staff_id
 * @property int $leadable_id
 * @property string $leadable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property LeadStatus[] $statuses
 * @property LeadStatus $status
 * @property DataFieldFeedback[] $data
 */
class Lead extends Model
{

    protected $attributes = [
        'data' => '[]',
    ];
    protected $casts = [
        'data' => DataFieldFeedbackCasts::class,
    ];

    const array TYPES = [
        FormBack::class => 'form',
    ];


    public function getType(): string
    {
        return self::TYPES[$this->leadable_type];
    }

    public static function register(): static
    {
        throw new \DomainException('Неверный вызов');

    }

    public function leadable(): MorphTo
    {
        return $this->morphTo();
    }

    public function setStatus(
        #[ExpectedValues(valuesFromClass: LeadStatus::class)] int $value): void
    {

        $this->statuses()->create(['value' => $value, 'created_at' => now()]);
    }

    public function status(): HasOne
    {
        return $this->hasOne(LeadStatus::class, 'lead_id', 'id')->latestOfMany();
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(LeadStatus::class, 'lead_id', 'id');
    }


    /**
     * @param DataFieldFeedback[] $data
     * @return void
     */
    public function create_feedback(array $data): void
    {
        $this->data = $data;
        $this->save();
        $this->refresh();
        $this->statuses()->save(LeadStatus::new());
    }

    public function getStatusName(): string
    {
        return $this->status->getName();
    }

    public function isNew(): bool
    {
        return $this->status->value == LeadStatus::STATUS_NEW && $this->staff_id == null;
    }


}
