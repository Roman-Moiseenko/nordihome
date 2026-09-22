<?php

declare(strict_types=1);

namespace App\Modules\Shop\Infrastructure\Jobs;

use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Content\Entity\Page;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Shared\Domain\ValueObjects\QueueName;
use App\Modules\Shop\Application\DTOs\ClientContext;
use App\Modules\Shop\Application\Queries\Category\CategoryIndexQuery;
use App\Modules\Shop\Application\Queries\Category\CategoryPageQuery;
use App\Modules\Shop\Application\Queries\Category\GetCategoryTreeQuery;
use App\Modules\Shop\Application\Queries\Ikea\GetIkeaTreeQuery;
use App\Modules\Shop\Application\Queries\Ikea\IkeaIndexQuery;
use App\Modules\Shop\Application\Queries\Ikea\IkeaViewQuery;
use App\Modules\Shop\Application\Queries\Menu\GetMenusQuery;
use App\Modules\Shop\Application\Queries\Page\PageViewQuery;
use App\Modules\Shop\Application\Queries\Promotion\PromotionPageQuery;
use App\Modules\Shop\Application\Queries\Room\GetRoomTreeQuery;
use App\Modules\Shop\Application\Queries\Room\RoomIndexQuery;
use App\Modules\Shop\Application\Queries\Room\RoomPageQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Прогрев (предзаполнение) всех кешей модуля Shop.
 *
 * Выполняется в фоне на очереди photo (самая низкоприоритетная, идёт последней),
 * чтобы «холодный» старт витрины после сброса кеша не создавал нагрузку на
 * веб-запросы.
 */
class WarmShopCacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $queue = QueueName::PHOTO;

    public function handle(
        GetCategoryTreeQuery  $categoryTreeQuery,
        GetRoomTreeQuery      $roomTreeQuery,
        GetIkeaTreeQuery      $ikeaTreeQuery,
        CategoryIndexQuery    $categoryIndexQuery,
        RoomIndexQuery        $roomIndexQuery,
        IkeaIndexQuery        $ikeaIndexQuery,
        GetMenusQuery         $menusQuery,
        CategoryPageQuery     $categoryPageQuery,
        RoomPageQuery         $roomPageQuery,
        IkeaViewQuery         $ikeaViewQuery,
        PromotionPageQuery    $promotionPageQuery,
        PageViewQuery         $pageViewQuery,
    ): void {
        $context = new ClientContext();
        $params = ['page' => 1];

        // 1. Глобальные кеши: деревья, индексные страницы, меню.
        $this->run(fn() => $categoryTreeQuery->execute(), 'category_tree');
        $this->run(fn() => $roomTreeQuery->execute(), 'room_tree');
        $this->run(fn() => $ikeaTreeQuery->execute(), 'ikea_tree');
        $this->run(fn() => $categoryIndexQuery->execute(), 'category_index_page');
        $this->run(fn() => $roomIndexQuery->execute(), 'room_index_page');
        $this->run(fn() => $ikeaIndexQuery->execute(), 'ikea_index_page');
        $this->run(fn() => $menusQuery->execute(), 'menus');

        // 2. Кеши страниц категорий (товары + фильтры).
        foreach (Category::query()->pluck('slug') as $slug) {
            $this->run(
                fn() => $categoryPageQuery->execute((string) $slug, $params, $context),
                'category:' . $slug,
            );
        }

        // 3. Кеши страниц комнат (товары + фильтры).
        foreach (Room::query()->pluck('slug') as $slug) {
            $this->run(
                fn() => $roomPageQuery->execute((string) $slug, $params, $context),
                'room:' . $slug,
            );
        }

        // 4. Кеши страниц ИКЕА-категорий (товары).
        foreach (ParserCategory::query()->pluck('slug') as $slug) {
            $this->run(
                fn() => $ikeaViewQuery->execute((string) $slug, $params),
                'ikea:' . $slug,
            );
        }

        // 5. Кеши страниц акций (товары + фильтры).
        foreach (Promotion::query()->pluck('slug') as $slug) {
            $this->run(
                fn() => $promotionPageQuery->execute((string) $slug, $params, $context),
                'promotion:' . $slug,
            );
        }

        // 6. Кеши контентных страниц (по slug).
        foreach (Page::query()->pluck('slug') as $slug) {
            $this->run(
                fn() => $pageViewQuery->execute((string) $slug),
                'page:' . $slug,
            );
        }
    }

    /**
     * Выполняет этап прогрева, не прерывая всю задачу при сбое одного элемента.
     */
    private function run(callable $step, string $label): void
    {
        try {
            $step();
        } catch (\Throwable $e) {
            Log::warning("Прогрев кеша Shop: сбой на этапе {$label}", [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
