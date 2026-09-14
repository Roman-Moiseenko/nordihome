<?php

namespace App\Modules\Shop\Application\Queries\Page;

use App\Modules\Content\Entity\Page;
use App\Modules\Content\Entity\Widgets\Template;
use App\Modules\Setting\Application\Actions\GetWebSettingsUseCase;
use App\Modules\Shop\Application\DTOs\PageElements\OgImage;
use App\Modules\Shop\Application\DTOs\Pages\PageViewPageData;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;
use JetBrains\PhpStorm\Deprecated;

readonly class PageViewQuery
{
    public function __construct(
        //private SchemaBuilder                 $schemaBuilder,
        private SeoAdapter                    $seoAdapter,
        private GetWebSettingsUseCase $webSettingsUseCase,
    )
    {
    }
    public function execute(string $slug):? PageViewPageData
    {
        $web = $this->webSettingsUseCase->execute();
        //MAINDO Сделать кеширование данных
        $page = Page::query()->where('slug', $slug)->where('published', true)->first();
        if (is_null($page)) return null;

        //Генерация текста
        $text = $this->renderTags($page->text);
        $text  = $this->renderRoots($text);
        $text = Template::renderClasses($text);


        $meta = $this->seoAdapter->getSeo('content.page', $page);
        if (!empty($page->meta->title)) $meta->title = $page->meta->title;
        if (!empty($page->meta->description)) $meta->description = $page->meta->description;

        $meta->canonical = route('shop.page.view', $page->slug);
        $meta->ogSiteName = $web->web_name;
        $meta->ogImages[] = new OgImage(
            url: $page->getImage(),
        );
        $meta->articleModifiedTime = $page->updated_at->toIso8601String();
        $meta->articlePublishedTime = $page->published_at->toIso8601String();

        //FixMe if (!is_null($page->image)) $meta->addImage(OgImage::fromData($page->image));

        //TODO Schema для страниц
        return new PageViewPageData(
            meta: $meta->asArticle(),
            name: $page->name,
            text: $text,
            template: Template::blade('page') . $page->template
        );
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

    #[Deprecated]
    private function renderRoots(string $text): string
    {
        $pattern = '/\[root=\"(.+?)\"]/su';
        preg_match_all($pattern, $text, $matches);
        $replaces = $matches[0]; //шот-коды вида [root="file"]
        $templates = $matches[1]; //значение template

        foreach ($templates as $key => $template) {
            $text = str_replace(
                $replaces[$key],
                view('shop.templates.' . $template)->render(),
                $text
            );
        }

        return $text;
    }
}
