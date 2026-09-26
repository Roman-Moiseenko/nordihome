<?php

declare(strict_types=1);

namespace App\Modules\Content\Console\Commands;

use App\Modules\Content\Domain\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Infrastructure\Services\WidgetFileService;
use Illuminate\Console\Command;

class SyncWidgetTemplatesToFilesCommand extends Command
{
    protected $signature = 'content:widget-template-export';
    protected $description = 'Записать содержимое поля template виджетов в файлы шаблонов (БД -> файл)';

    public function __construct(
        private readonly WidgetRepositoryInterface $widgetRepository,
        private readonly WidgetFileService $widgetFileService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $written = 0;

        foreach ($this->widgetRepository->getAll() as $widget) {
            $category = (string) $widget->category;
            $slug = $widget->slug;

            $fileExists = $this->widgetFileService->templateFileExists($category, $slug);
            $content = $this->widgetFileService->getContent($category, $slug);
            $isEmpty = $content === '';

            // Если файла нет или он пустой, а в БД есть данные — создаём файл и записываем данные.
            if ((!$fileExists || $isEmpty) && $widget->template !== null && $widget->template !== '') {
                $this->widgetFileService->saveContent($category, $slug, $widget->template);
                $written++;
            }
        }

        $this->info("Создано/записано файлов: {$written}.");

        return self::SUCCESS;
    }
}
