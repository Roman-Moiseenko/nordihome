<?php


namespace App\Modules\Product\Entity;


use App\Entity\Photo;
use App\Entity\Video;
use App\Modules\User\Entity\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property string $text
 * @property int $rating
 * @property int $status
 * @property bool $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Photo[] $photos
 * @property Video[] $videos
 */
class Review extends Model
{
    const STATUS_DRAFT = 5501;
    const STATUS_MODERATION = 5502;
    const STATUS_PUBLISHED = 5503;
    const STATUS_BLOCKED = 5504;
    const STATUSES = [
        self::STATUS_DRAFT => 'Черновик',
        self::STATUS_MODERATION => 'На модерации',
        self::STATUS_PUBLISHED => 'Опубликован',
        self::STATUS_BLOCKED => 'Заблокирован',
    ];

    protected $table = 'product_reviews';
    protected $fillable = [
        'product_id',
        'user_id',
        'text',
        'rating',
        'active',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function empty(int $product_id, int $user_id):self
    {
        return self::create([
            'product_id' => $product_id,
            'user_id' => $user_id,
            'text' => '',
            'rating' => 5,
            'status' => self::STATUS_DRAFT,
        ]);
    }

    public static function register(int $product_id, int $user_id, string $text, int $rating): self
    {
        return self::create([
            'product_id' => $product_id,
            'user_id' => $user_id,
            'text' => $text,
            'rating' => $rating,
            'status' => self::STATUS_MODERATION,
        ]);
    }

    //*** RELATIONS
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function photos()
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('sort');;
    }

    public function videos()
    {
        return $this->morphMany(Video::class, 'videoable');
    }

    //*** Helpers
    public function htmlDate(): string
    {
        return $this->created_at->translatedFormat('j F Y H:i');
    }

    public function statusHtml(): string
    {
        return self::STATUSES[$this->status];
    }
}
