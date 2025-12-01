<?php

namespace App\Modules\Feedback\Entity;

use App\Modules\Feedback\Classes\DataFieldFeedback;
use App\Modules\Lead\Traits\LeadField;
use App\Modules\Page\Entity\FormWidget;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;


/**
 * @property int $id
 * @property string $url
 * @property Carbon $created_at
 * @property array $data_form - оригинальные данные с формы
 * @property int $widget_id
 * @property FormWidget $widget
 */
class FormBack extends Model implements IFeedback
{

    use LeadField;

    public $timestamps = false;

    protected $table = 'feedback_forms';

    protected $attributes = [
        'data_form' => '{}',
    ];
    protected $fillable = [
        'widget_id',
        'url',
        'created_at',
    ];
    protected $casts = [
        'data_form' => 'json',
    ];

    public static function register(int $widget_id, string $url): static
    {
        return self::create([
            'url' => $url,
            'widget_id' => $widget_id,
            'created_at' => now(),
        ]);
    }

    public function widget(): BelongsTo
    {
        return $this->belongsTo(FormWidget::class, 'widget_id', 'id');
    }

    public function date(): Carbon
    {
        return $this->created_at;
    }

    /**
     * @return DataFieldFeedback[]
     */
    public function data(): array
    {
        $result = [];
        foreach ($this->data_form as $key => $value) {
            $field = DataFieldFeedback::create(slug: $key, value: $value ?? '');
            $field->name = $this->widget->getFieldName($key);
            $result[] = $field;
        }
        return $result;
    }
}
