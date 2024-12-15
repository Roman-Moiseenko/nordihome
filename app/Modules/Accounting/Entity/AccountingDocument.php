<?php

namespace App\Modules\Accounting\Entity;

use App\Modules\Admin\Entity\Admin;
use App\Traits\HtmlInfoData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Blueprint;
use JetBrains\PhpStorm\Pure;

/**
 * @property int $id
 * @property string $number
 * @property string $incoming_number
 * @property Carbon $incoming_at
 * @property bool $completed
 * @property string $comment
 * @property int $staff_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Admin $staff
 * @property AccountingProduct[] $products
 */
abstract class AccountingDocument extends Model
{
    use HtmlInfoData;

    protected string $prefix = '';
    protected string $blank = 'Документ';

    /**
     * Объединяем базовые параметры
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $casts = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'incoming_at' => 'datetime',
        ];
        $fillable = [
            'number',
            'incoming_number',
            'incoming_at',
            'completed',
            'comment',
            'staff_id',
        ];
        $attributes = [
            'completed' => false,
            'comment' => '',
        ];

        $this->casts = array_merge($this->casts, $casts);
        $this->fillable = array_merge($this->fillable, $fillable);
        $this->attributes = array_merge($this->attributes, $attributes);
    }

    public static function baseNew(int $staff_id): static
    {
        $base = self::make([
            'staff_id' => $staff_id,
            'completed' => false,
            'comment' => '',
        ]);

        $base->generateNumber();
        return $base;
    }

    //IS...

    /**
     * Документ проведен
     */
    final public function isCompleted(): bool
    {
        return $this->completed == true;
    }

    final public function isProduct(int $product_id): bool
    {
        foreach ($this->products as $item) {
            if ($item->product_id == $product_id) return true;
        }
        return false;
    }

    //SET...

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
        $this->save();
    }

    /**
     * Провести документ
     */
    final public function completed(): void
    {
        $this->completed = true;
        $this->save();
    }

    /**
     * Вернуть в работу
     */
    final public function work(): void
    {
        $this->completed = false;
        $this->save();

    }

    //GET...
    public function documentName(): string
    {
        return $this->blank . ' ' . $this->number . ' от ' . $this->created_at->translatedFormat('d-m-Y');
    }

    abstract function documentUrl(): string;

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getProduct(int $product_id): ?AccountingProduct
    {
        foreach ($this->products as $item) {
            if ($item->product_id == $product_id) return $item;
        }
        return null;
    }

    protected function generateNumber(): void
    {
        $year = now()->year;
        $begin = Carbon::parse('01/01/' . $year);
        $end = Carbon::parse('01/01/' . ($year + 1));
        //Нумерация только по текущему году
        $count = self::where('created_at', '>=', $begin)->where('created_at', '<', $end)->count();

        $this->number = $this->prefix . ($count + 1);
    }

    final public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    final public function scopeWork($query)
    {
        return $query->where('completed', false);
    }

    #[Pure]
    public function status(): string
    {
        if ($this->isCompleted()) {
            return 'Проведен';
        } else {
            return 'В работе';
        }
    }

    //RELATION...
    final public function staff(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'staff_id', 'id');
    }

    abstract public function products(): HasMany;

    final public function baseSave(array $document): void
    {
        $this->number = $document['number'] ?? '';
        $this->created_at = $document['created_at'];
        $this->incoming_number = $document['incoming_number'] ?? '';
        $this->incoming_at = $document['incoming_at'] ?? null;
        $this->comment = $document['comment'] ?? '';
    }

    //Для создания таблиц
    final public static function columns(Blueprint $table): void
    {
        $table->string('number')->default('');
        $table->string('incoming_number')->default('');
        $table->boolean('completed')->default(false);
        $table->timestamp('incoming_at')->nullable();
        $table->timestamps();
        $table->text('comment');
        $table->foreignId('staff_id')->constrained('admins')->onDelete('restrict');
    }

    final public static function dropColumns(Blueprint $table): void
    {
        $table->dropIndex('number');
        $table->dropColumn('incoming_number');
        $table->dropColumn('completed');
        $table->dropColumn('incoming_at');
        $table->dropColumn('created_at');
        $table->dropColumn('updated_at');

        $table->dropForeign(['staff_id']);
        $table->dropColumn('staff_id');
    }

    /**
     * Список всех дочерних документов (дерево)
     */
    abstract public function onBased(): ?array;

    /**
     * Список документов базовых (default = 1)
     */
    abstract public function onFounded(): ?array;

    final protected function basedItem(?AccountingDocument $document): array
    {
        if (is_null($document)) return [];
        $result = [
            'label' => $document->documentName(),
            'url' => $document->documentUrl(),
        ];
        if (!empty($document->onBased())) $result['children'] = $document->onBased();
        return $result;
    }

    final protected function foundedGenerate(mixed $document):? array
    {
        if (is_null($document)) return null;
        if (is_array($document)) {
            $result = [];
            foreach ($document as $item) {
                $result[] = [
                    'label' => $item->documentName(),
                    'url' => $item->documentUrl(),
                ];
            }
            return $result;

        } else {
            return [
                [
                    'label' => $document->documentName(),
                    'url' => $document->documentUrl(),
                ]
            ];
        }
    }
}
