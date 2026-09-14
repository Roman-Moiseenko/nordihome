<?php

declare(strict_types=1);

namespace App\Modules\Shop\Application\Queries\Post;

use App\Modules\Setting\Application\Actions\GetWebSettingsUseCase;
use App\Modules\Shop\Application\DTOs\Elements\TableContent;
use App\Modules\Shop\Application\DTOs\PageElements\OgImage;
use App\Modules\Shop\Application\DTOs\Pages\PostViewPageData;
use App\Modules\Shop\Application\Services\WidgetDataEnricherService;
use App\Modules\Shop\Infrastructure\Persistence\Builders\SchemaBuilder;
use App\Modules\Shop\Infrastructure\Persistence\Query\ContentBlockQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\Query\PostViewQueryRepository;
use App\Modules\Shop\Infrastructure\Persistence\SeoAdapter;
use Illuminate\Support\Str;

readonly class PostPageQuery
{
    public function __construct(
        private PostViewQueryRepository       $postRepository,
        private ContentBlockQueryRepository   $blockRepository,
        private SeoAdapter                    $seoAdapter,
        private SchemaBuilder                 $schemaBuilder,
        private WidgetDataEnricherService     $widgetEnricher,
        private GetWebSettingsUseCase $webSettingsUseCase,
    )
    {
    }

    public function execute(string $slug): PostViewPageData
    {
        $web = $this->webSettingsUseCase->execute();
        // 1. Получить PostData (один SQL-запрос с фото)
        $post = $this->postRepository->getPostBySlug($slug);

        // 2. Получить все контент-блоки для поста (один SQL-запрос, сортировка по sort_order)
        $blocks = $this->blockRepository->getBlocksByContainer('post', $post->id);

        // 3. Обогатить каждый блок: заменить ID дочерних виджетов на WidgetPageData
        foreach ($blocks as $block) {
            $block->widget = $this->widgetEnricher->enrich($block->widget);
        }
        // 4. SEO
        $meta = $this->seoAdapter->getSeo('content.post', $post);
        $meta->canonical = route('shop.post.view', $post->slug);
        $meta->ogSiteName = $web->web_name;
        $meta->addImage(OgImage::fromData($post->image));
        $meta->articleModifiedTime = $post->updatedAt;
        $meta->articlePublishedTime = $post->publishedAt;

        // вытаскиваем FAQ из блоков, если есть
        $faq = [];
        foreach ($blocks as $block) {
            if ($block->widget->slug == 'faq') {
                $faq = $block->widget->params['items'];
                break;
            }
        }
        // вытаскиваем содержание из блоков
        $tableContents = [];
        foreach ($blocks as $block) {
            if ($block->widget->slug == 'heading') {
                if ($block->widget->params['tag'] == 'h2') {
                    $text = trim($block->widget->params["part1"]["text"] . ' ' . $block->widget->params["part2"]["text"]);
                    $tableContents[] = new TableContent(
                        id: Str::slug($text),
                        title: $text,
                    );
                }
            }
        }

        // 5. Schema
        $schema = $this->schemaBuilder->buildForPost($post, $faq);

        return new PostViewPageData(
            post: $post,
            blocks: $blocks,
            meta: $meta->asArticle(),
            schema: $schema,
            tableContents: $tableContents,
        );
    }
}
