<?php


namespace App\Modules\Product\Entity;

use App\Entity\Photo;
use App\Entity\Video;
use App\Modules\Discount\Entity\DiscountReview;
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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Photo $photo
 * @property Video $video
 * @property DiscountReview $discount
 *
 * @property User $user
 * @property Product $product
 */
class Review extends Model
{
    const STATUS_DRAFT = 5501;
    const STATUS_MODERATED = 5502;
    const STATUS_PUBLISHED = 5503;
    const STATUS_BLOCKED = 5504;
    const STATUSES = [
        self::STATUS_DRAFT => 'Черновик',
        self::STATUS_MODERATED => 'На модерации',
        self::STATUS_PUBLISHED => 'Опубликован',
        self::STATUS_BLOCKED => 'Заблокирован',
    ];

    protected $table = 'product_reviews';
    protected $fillable = [
        'product_id',
        'user_id',
        'text',
        'rating',
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
            'status' => self::STATUS_MODERATED,
        ]);
    }

    //*** IS-...

    public function isDraft(): bool
    {
        return $this->status == self::STATUS_DRAFT;
    }

    public function isModerated(): bool
    {
        return $this->status == self::STATUS_MODERATED;
    }

    public function isPublished(): bool
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    public function isBlocked(): bool
    {
        return $this->status == self::STATUS_BLOCKED;
    }


    public function isProduct(int $product_id): bool
    {
        return $this->product_id == $product_id;
    }

    //*** RELATIONS
    public function discount()
    {
        return $this->hasOne(DiscountReview::class, 'review_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function photo()
    {
        return $this->morphOne(Photo::class, 'imageable');//->orderBy('sort');;
    }

    public function video()
    {
        return $this->morphOne(Video::class, 'videoable');
    }

    //*** Helpers
    public function htmlDate(): string
    {
        if ($this->updated_at == null) {
            return $this->created_at->translatedFormat('j F Y H:i');
        } else {
            return $this->updated_at->translatedFormat('j F Y H:i');
        }
    }

    public function statusHtml(): string
    {
        return self::STATUSES[$this->status];
    }

}
