<?php

namespace App\Modules\Page\Entity\Renders;

use App\Modules\Base\Casts\MetaCast;
use App\Modules\Base\Entity\Meta;
use App\Modules\Page\Entity\Template;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use function Termwind\render;

/**
 * @property string $text
 * @property bool $published
 * @property Carbon $published_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Meta $meta
 */
abstract class RenderPage extends Model
{
    protected string $field = '';

    protected $attributes = [
        'meta' => '{}',
        'text' => '',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'meta' => MetaCast::class,
    ];

    public function published(): void
    {
        if ($this->published_at == null) $this->published_at = now();
        $this->published = true;
    }

    public function draft(): void
    {
        $this->published = false;
    }

    public function getFragment(): string
    {
        $text = trim($this->text);
        $pos = mb_strpos($text, "\n");

        return !$pos ? $text : substr($text, 0, $pos);
    }

    /**
     * @throws \Throwable
     */
    public function view(callable $fn): string
    {

        $this->field = empty($this->field) ? $this->getField() : $this->field;

        $this->text = $this->renderTags($this->text);
        $this->text = Template::renderClasses($this->text);

        $url_page = route('shop.' . $this->field . '.view', $this->slug);
        if ($fn != null) $this->meta = $fn($this, $this->meta);

        return view(
            Template::blade($this->field) . $this->template,
            [$this->field => $this, 'title' => $this->meta->title, 'description' => $this->meta->description, 'url_page' => $url_page])
            ->render();
    }

    private function renderTags(string $text): string
    {
        //<div>
        $pattern = '/\[div=\"(.+?)\"(.*?)\]/su';
        preg_match_all($pattern, $text, $matches);

        $replaces = $matches[0]; //шот-коды вида [div="class"] (массив)
        $classes = $matches[1]; //значение classes
        $add = $matches[2];

        foreach ($classes as $key => $class) {
            $text = str_replace(
                $replaces[$key],
                '<div class="' . $class . '"' . $add[$key] . '>',
                $text);
        }
        //</div>

        return str_replace('[/div]', '</div>', $text);
    }

    public function scopeActive($query)
    {
        return $query->where('published', true);
    }

    private function getField(): string
    {
        $class_parts = explode('\\', get_class($this));
        $str = end($class_parts);

        //$str = str_replace(__NAMESPACE__ . "\\", '', get_class($this));
        return strtolower($str);
    }
}
