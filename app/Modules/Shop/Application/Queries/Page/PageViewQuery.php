<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries\Page;

use App\Modules\Content\Entity\Widgets\Template;
use App\Modules\Setting\Application\Actions\GetWebSettingsUseCase;
use App\Modules\Shop\Application\DTOs\PageElements\OgImage;
use App\Modules\Shop\Application\DTOs\Pages\PageViewPageData;
use App\Modules\Shop\Application\Services\WidgetDataEnricherService;
use App\Modules\Shop\Domain\Schema\SchemaData;
use App\Modules\Shop\Infrastructure\Persistence\Query\ContentBlockQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\PageViewQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;
use JetBrains\PhpStorm\Deprecated;

readonly class PageViewQuery
{
    public function __construct(
        private PageViewQueryRepository     $pageRepository,
        private SeoAdapter                  $seoAdapter,
        private GetWebSettingsUseCase       $webSettingsUseCase,
        private ContentBlockQueryRepository $blockRepository,
        private WidgetDataEnricherService   $widgetEnricher,
    )
    {
    }

    public function execute(string $slug): ?PageViewPageData
    {
        $web = $this->webSettingsUseCase->execute();
        // 1. Получить PageData (один SQL-запрос)
        $page = $this->pageRepository->getPageBySlug($slug);
        if (is_null($page)) return null;

        $text = '';
        $blocks = [];

        if ($page->oldRender) {//Генерация текста
            $text = $this->renderTags($page->text);
            $text = $this->renderRoots($text);
            $text = Template::renderClasses($text);
            $page->text = $text;

            $template = Template::blade('page') . $page->template;
        } else {
            $blocks = $this->blockRepository->getBlocksByContainer('page', $page->id);
            try {
                foreach ($blocks as $index => $block) {
                    $block->widget = $this->widgetEnricher->enrich($block->widget);
                }
                //FixMe Удалить потом
            } catch (\Throwable $exception) {
                dd('Ошибка в блоке #' . $index + 1);
            }

            $template = 'shop.content.page';
        }

        // 4. SEO
        $meta = $this->seoAdapter->getSeo('content.page', $page);

        $meta->canonical = route('shop.page.view', $page->slug);
        $meta->ogSiteName = $web->web_name;

        if (!empty($page->image)) $meta->addImage(OgImage::fromData($page->image));
        $meta->articleModifiedTime = $page->updatedAt;
        $meta->articlePublishedTime = $page->publishedAt;

        //TODO Schema для страниц
        return new PageViewPageData(
            page: $page,
            meta: $meta->asArticle(),
            name: $page->name,
            text: $text,
            template: $template,
            blocks: $blocks,
            schema: new SchemaData(),
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
