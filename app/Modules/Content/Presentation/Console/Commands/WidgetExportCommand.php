<?php

declare(strict_types=1);

namespace App\Modules\Content\Presentation\Console\Commands;

use App\Modules\Content\Domain\Interfaces\WidgetRepositoryInterface;
use DateTimeInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WidgetExportCommand extends Command
{
    protected $signature = 'content:widget-export {--file=widgets.json : Имя файла в папке public/temp}';
    protected $description = 'Выгрузить данные виджетов из БД в JSON-файл (БД -> public/temp)';

    private const string TEMP_DIR = 'temp';

    public function __construct(
        private readonly WidgetRepositoryInterface $widgetRepository,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $data = array_map(
            fn($widget) => [
                'name' => $widget->name,
                'slug' => $widget->slug,
                'description' => $widget->description,
                'category' => (string) $widget->category,
                'schema' => $widget->schema->toArray(),
                'template' => $widget->template,
                'updated_at' => $widget->updatedAt?->format(DateTimeInterface::ATOM),
            ],
            $this->widgetRepository->getAll(),
        );

        $fileName = (string) $this->option('file');
        $path = public_path(self::TEMP_DIR . '/' . $fileName);

        File::ensureDirectoryExists(dirname($path));
        File::put(
            $path,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        );

        $this->info('Выгружено виджетов: ' . count($data) . ". Файл: {$path}.");

        return self::SUCCESS;
    }
}
