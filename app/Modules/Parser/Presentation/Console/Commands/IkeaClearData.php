<?php

namespace App\Modules\Parser\Presentation\Console\Commands;

use App\Console\CreatesApplication;
use App\Modules\Parser\Infrastructure\Models\ParserCategory;
use App\Modules\Parser\Infrastructure\Models\ParserLog;
use App\Modules\Parser\Infrastructure\Models\ParserLogItem;
use App\Modules\Parser\Infrastructure\Models\ParserProduct;
use App\Modules\Shared\Infrastructure\Models\Photo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Очистка данных парсера: товары, категории, фото, файлы, кеш
 *
 * --param=product — только товары (parser_products, parser_logs, parser_categories_products)
 * --param=all     — товары + категории (parser_categories)
 */
class IkeaClearData extends Command
{
    use CreatesApplication;

    protected $signature = 'ikea:clear {--param=product : Режим очистки (product / all)}';

    protected $description = 'Очистка данных парсера Икеа: товары, категории, фото, файлы и кеш';

    private const string MODEL_TYPE_PRODUCT = 'parser.product';
    private const string MODEL_TYPE_CATEGORY = 'parser.category';

    public function handle(): void
    {
        $param = $this->option('param');

        if (!in_array($param, ['product', 'all'], true)) {
            $this->error('Недопустимый параметр --param. Допустимые значения: product, all');
            return;
        }

        $this->info('Начинаем очистку данных парсера (режим: ' . $param . ')...');

        // Шаг 1: Удаляем фото товаров из БД (с физическим удалением файлов через boot-события)
        $this->deletePhotosByModelType(self::MODEL_TYPE_PRODUCT, 'товаров');

        // Шаг 2: Удаляем parser_log_items
        $countItems = ParserLogItem::query()->delete();
        $this->info("Удалено записей parser_log_items: {$countItems}");

        // Шаг 3: Удаляем parser_logs
        $countLogs = ParserLog::query()->delete();
        $this->info("Удалено записей parser_logs: {$countLogs}");

        // Шаг 4: Удаляем связи в pivot-таблице
        $countPivot = DB::table('parser_categories_products')->delete();
        $this->info("Удалено связей parser_categories_products: {$countPivot}");

        // Шаг 5: Удаляем parser_products
        $countProducts = ParserProduct::query()->delete();
        $this->info("Удалено записей parser_products: {$countProducts}");

        // Шаг 6: Физически удаляем папки uploads и cache для товаров
        $this->deleteDirectory(public_path('uploads/parser/product'), 'uploads/parser/product');
        $this->deleteDirectory(public_path('cache/parser/product'), 'cache/parser/product');

        if ($param === 'all') {
            // Шаг 7: Удаляем фото категорий из БД
            $this->deletePhotosByModelType(self::MODEL_TYPE_CATEGORY, 'категорий');

            // Шаг 8: Удаляем parser_categories
            $countCategories = ParserCategory::query()->delete();
            $this->info("Удалено записей parser_categories: {$countCategories}");

            // Шаг 9: Физически удаляем папки uploads и cache для категорий
            $this->deleteDirectory(public_path('uploads/parser/category'), 'uploads/parser/category');
            $this->deleteDirectory(public_path('cache/parser/category'), 'cache/parser/category');
        }

        // Очистка кеша страниц (после удаления данных парсера страницы могут кешировать списки)
        $this->info('Очищаем кеш приложения...');
        $this->call('cache:clear');
        $this->info('Кеш приложения очищен.');

        $this->info('Очистка данных парсера завершена.');
    }

    /**
     * Удалить все фото с указанным model_type.
     * Удаление через модель запускает boot-событие deleting,
     * которое очищает thumb-файлы и оригинальный файл.
     */
    private function deletePhotosByModelType(string $modelType, string $label): void
    {
        $photos = Photo::where('model_type', $modelType)->get();
        $count = $photos->count();

        if ($count === 0) {
            $this->info("Фото для {$label} не найдены.");
            return;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($photos as $photo) {
            $photo->delete();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Удалено фото {$label}: {$count}");
    }

    /**
     * Рекурсивно удалить директорию, если она существует.
     */
    private function deleteDirectory(string $path, string $label): void
    {
        if (is_dir($path)) {
            File::deleteDirectory($path);
            $this->info("Папка удалена: {$label}");
        } else {
            $this->line("Папка не найдена (пропущено): {$label}");
        }
    }
}
