<?php

declare(strict_types=1);

namespace App\Modules\Content\Presentation\Console\Commands;

use App\Modules\Content\Domain\Entities\WidgetEntity;
use App\Modules\Content\Domain\Interfaces\WidgetRepositoryInterface;
use App\Modules\Content\Domain\ValueObjects\WidgetCategory;
use App\Modules\Content\Domain\ValueObjects\WidgetSchema;
use App\Modules\Content\Infrastructure\Services\WidgetFileService;
use DateTimeImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WidgetImportCommand extends Command
{
    protected $signature = 'content:widget-import {--file=widgets.json : Имя файла в папке public/temp}';
    protected $description = 'Загрузить данные виджетов из JSON-файла (public/temp -> БД)';

    private const string TEMP_DIR = 'temp';

    public function __construct(
        private readonly WidgetRepositoryInterface $widgetRepository,
        private readonly WidgetFileService $widgetFileService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $fileName = (string) $this->option('file');
        $path = public_path(self::TEMP_DIR . '/' . $fileName);

        if (!File::exists($path)) {
            $this->error("Файл не найден: {$path}");

            return self::FAILURE;
        }

        $decoded = json_decode(File::get($path), true);

        if (!is_array($decoded)) {
            $this->error('Некорректный JSON в файле.');

            return self::FAILURE;
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($decoded as $index => $item) {
            if (!is_array($item) || !isset($item['slug']) || trim((string) $item['slug']) === '') {
                $this->warn("Пропущена запись #{$index}: отсутствует slug.");
                $skipped++;
                continue;
            }

            $name = (string) ($item['name'] ?? '');
            $slug = (string) $item['slug'];
            $category = (string) ($item['category'] ?? WidgetCategory::CUSTOM);

            $existing = $this->widgetRepository->getBySlug($slug);

            // Slug не найден на сервере — создаём новый виджет.
            if ($existing === null) {
                $entity = $this->buildEntity($item);
                $this->widgetRepository->save($entity);
                $this->widgetFileService->saveContent($category, $slug, $entity->template ?? '');
                $created++;
                $this->info("Создан виджет «{$name}» (slug: {$slug}).");
                continue;
            }

            // Slug совпадает — сравниваем версии по updated_at.
            $serverUpdatedAt = $existing->updatedAt?->getTimestamp() ?? 0;
            $fileUpdatedAt = $this->parseUpdatedAt($item['updated_at'] ?? null)?->getTimestamp() ?? 0;

            if ($serverUpdatedAt >= $fileUpdatedAt) {
                $this->line("Пропущен «{$name}» (slug: {$slug}): версия на сервере не старше.");
                $skipped++;
                continue;
            }

            // Версия в файле новее — спрашиваем подтверждение на замену.
            $this->line("Виджет «{$name}» (slug: {$slug}): версия в файле новее версии на сервере.");

            if (!$this->confirm('Заменить этот виджет?', false)) {
                $this->line("Пропущен «{$name}» (slug: {$slug}).");
                $skipped++;
                continue;
            }

            $entity = $this->buildEntity($item, $existing);
            $this->widgetRepository->save($entity);
            $this->widgetFileService->saveContent($category, $slug, $entity->template ?? '');
            $updated++;
            $this->info("Обновлён виджет «{$name}» (slug: {$slug}).");
        }

        $this->info("Итог: создано {$created}, обновлено {$updated}, пропущено {$skipped}.");

        return self::SUCCESS;
    }

    /**
     * @param array<string, mixed> $item
     */
    private function buildEntity(array $item, ?WidgetEntity $existing = null): WidgetEntity
    {
        $schema = is_array($item['schema'] ?? null)
            && isset($item['schema']['type'], $item['schema']['properties'])
            ? $item['schema']
            : ['type' => 'object', 'properties' => []];

        $name = (string) ($item['name'] ?? '');
        $slug = (string) $item['slug'];
        $category = new WidgetCategory((string) ($item['category'] ?? WidgetCategory::CUSTOM));
        $description = isset($item['description']) ? (string) $item['description'] : null;
        $template = isset($item['template']) ? (string) $item['template'] : null;

        if ($existing === null) {
            return new WidgetEntity(
                name: $name,
                slug: $slug,
                category: $category,
                schema: WidgetSchema::fromArray($schema),
                description: $description,
                template: $template,
            );
        }

        $existing->name = $name;
        $existing->slug = $slug;
        $existing->category = $category;
        $existing->schema = WidgetSchema::fromArray($schema);
        $existing->description = $description;
        $existing->template = $template;

        return $existing;
    }

    private function parseUpdatedAt(mixed $value): ?DateTimeImmutable
    {
        if (!is_string($value) || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
