<?php

declare(strict_types=1);

namespace App\Modules\Content\Console\Commands;

use App\Modules\Content\Domain\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Infrastructure\Services\WidgetFileService;
use Illuminate\Console\Command;

class SyncWidgetTemplatesFromFilesCommand extends Command
{
    protected $signature = 'content:widget-template-import';
    protected $description = 'Скопировать содержимое файлов шаблонов виджетов в поле template (файл -> БД)';

    public function __construct(
        private readonly WidgetRepositoryInterface $widgetRepository,
        private readonly WidgetFileService $widgetFileService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $updated = 0;
        $skipped = 0;

        foreach ($this->widgetRepository->getAll() as $widget) {
            $category = (string) $widget->category;
            $slug = $widget->slug;

            $fileExists = $this->widgetFileService->templateFileExists($category, $slug);
            $content = $this->widgetFileService->getContent($category, $slug);
            $isEmpty = $content === '';

            // Если файла нет или он пустой, а в БД уже есть данные — не перезаписываем поле.
            if ((!$fileExists || $isEmpty) && $widget->template !== null && $widget->template !== '') {
                $skipped++;
                continue;
            }

            $widget->template = $isEmpty ? null : $content;
            $this->widgetRepository->save($widget);
            $updated++;
        }

        $this->info("Обновлено виджетов: {$updated}. Пропущено: {$skipped}.");

        return self::SUCCESS;
    }
}
