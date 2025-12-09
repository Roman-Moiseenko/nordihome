<?php
declare(strict_types=1);

namespace App\Modules\Page\Entity;

use App\Modules\Base\Casts\MetaCast;
use App\Modules\Base\Entity\Meta;
use App\Modules\Base\Traits\IconField;
use App\Modules\Base\Traits\ImageField;
use App\Modules\Page\Entity\Renders\RenderPage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $template
 * @property string $description
 * @property PostCategory $category
 *
 */
class Post extends RenderPage
{
    use ImageField, IconField;

    protected $fillable = [
        'name',
        'slug',
        'template',
    ];

    public static function new(string $name, string $template): static
    {
        return self::make([
            'name' => $name,
            'template' => $template,
            'slug' => Str::slug($name),
        ]);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'category_id', 'id');
    }


    public function getParagraphs(int $numParagraphs = 1): string
    {
        // Проверка на корректное значение numParagraphs
        if ($numParagraphs < 1) {
            return ''; // Или можно выбросить исключение, если это некорректная ситуация
        }

        // Разделяем текст на абзацы.
        // Используем комбинации \r\n, \n или \r для разных ОС.
        // PREG_SPLIT_NO_EMPTY удаляет пустые строки, которые могут возникнуть от двойных переносов.
        $paragraphs = preg_split("/\r\n|\n|\r/", $this->text, -1, PREG_SPLIT_NO_EMPTY);

        // Если текста нет или он не содержит абзацев, возвращаем пустую строку.
        if (empty($paragraphs)) {
            return '';
        }

        // Выбираем только первые необходимые абзацы.
        $selectedParagraphs = array_slice($paragraphs, 0, $numParagraphs);

        // Объединяем выбранные абзацы обратно в строку, разделяя их двойным переносом строки
        // для сохранения визуального разделения.
        return implode("\n\n", $selectedParagraphs);
    }
}
