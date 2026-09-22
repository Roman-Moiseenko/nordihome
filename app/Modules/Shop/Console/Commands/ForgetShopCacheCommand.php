<?php

declare(strict_types=1);

namespace App\Modules\Shop\Console\Commands;

use App\Modules\Catalog\Infrastructure\Models\Category;
use App\Modules\Catalog\Infrastructure\Models\Group;
use App\Modules\Catalog\Infrastructure\Models\Room;
use App\Modules\Content\Entity\Page;
use App\Modules\Discount\Infrastructure\Models\Promotion;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Shop\Infrastructure\Persistence\CacheInvalidationRegistry;
use Illuminate\Console\Command;

class ForgetShopCacheCommand extends Command
{
    protected $signature = 'shop:cache-clear';
    protected $description = 'Сброс всех кешей модуля Shop по очереди';

    public function __construct(
        private readonly CacheInvalidationRegistry $registry,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Начинаем сброс кешей модуля Shop...');

        // 1. Категории (дерево, товары, фильтры, индексная страница + фиды + sitemap)
        foreach (Category::query()->pluck('id') as $id) {
            $this->registry->forgetCategory((int) $id);
        }
        $this->info('Кеши категорий сброшены');

        // 2. Комнаты (дерево, товары, фильтры, индексная страница + фиды + sitemap)
        foreach (Room::query()->pluck('id') as $id) {
            $this->registry->forgetRoom((int) $id);
        }
        $this->info('Кеши комнат сброшены');

        // 3. Категории ИКЕА (дерево, товары + фиды)
        foreach (ParserCategory::query()->pluck('id') as $id) {
            $this->registry->forgetIkeaCategory((int) $id);
        }
        $this->info('Кеши ИКЕА-категорий сброшены');

        // 4. Акции (товары, фильтры + фиды + sitemap)
        foreach (Promotion::query()->pluck('id') as $id) {
            $this->registry->forgetPromotion((int) $id);
        }
        $this->info('Кеши акций сброшены');

        // 4. Группы  (товары, фильтры + фиды + sitemap)
        foreach (Group::query()->pluck('id') as $id) {
            $this->registry->forgetGroup((int) $id);
        }
        $this->info('Кеши групп сброшены');

        // 5. Контентные страницы (по slug + sitemap)
        foreach (Page::query()->pluck('slug') as $slug) {
            $this->registry->forgetPage((string) $slug);
        }
        $this->info('Кеши страниц сброшены');

        // 6. Глобальные кеши (деревья, индексные страницы, меню, контакты, фиды, sitemap)
        $this->registry->forgetAll();
        $this->info('Глобальные кеши сброшены');

        $this->info('Все кеши модуля Shop сброшены.');

        return self::SUCCESS;
    }
}
