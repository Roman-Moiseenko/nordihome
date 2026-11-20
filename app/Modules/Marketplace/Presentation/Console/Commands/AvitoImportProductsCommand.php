<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Presentation\Console\Commands;

use App\Modules\Marketplace\Application\Services\Avito\AvitoProductImportLogger;
use App\Modules\Marketplace\Infrastructure\Jobs\ImportAvitoProductSheetJob;
use App\Modules\Shared\Domain\ValueObjects\QueueName;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Загрузка объявлений Авито из xlsx-выгрузки.
 *
 * Команда только читает файл (без обращений к БД) и для каждого листа
 * объявлений ставит в очередь ImportAvitoProductSheetJob.
 */
class AvitoImportProductsCommand extends Command
{
    protected $signature = 'avito:import-products {--file=public/temp/avito_unload.xlsx : Путь к файлу выгрузки}';

    protected $description = 'Загружает объявления Авито из xlsx-выгрузки';

    public function __construct(
        private readonly AvitoProductImportLogger $logger,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $file = $this->option('file');
        $path = realpath($file);

        if ($path === false || !is_file($path)) {
            $this->error("Файл не найден: {$file}");

            return self::FAILURE;
        }

        $this->logger->start();

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        $dispatched = 0;

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            if ($this->isServiceSheet($sheet->getTitle())) {
                continue;
            }

            $categoryPath = trim((string) $sheet->getCell([1, 1])->getValue());
            if ($categoryPath === '') {
                continue;
            }

            [$parameters, $rows] = $this->readSheet($sheet);

            if (empty($rows)) {
                $this->warn("Лист «{$sheet->getTitle()}» не содержит строк объявлений — пропущен.");
                continue;
            }

            ImportAvitoProductSheetJob::dispatch($categoryPath, $sheet->getTitle(), $parameters, $rows)
                ->onQueue(QueueName::DEFAULT);

            $dispatched++;
        }

        $this->info("Поставлено задач в очередь \"" . QueueName::DEFAULT . "\": {$dispatched} листов.");
        $this->info("Лог загрузки: {$this->logger->getFilePath()}");

        return self::SUCCESS;
    }

    private function isServiceSheet(string $title): bool
    {
        $title = trim($title);

        return $title === 'Инструкция'
            || str_starts_with($title, 'Спр')
            || str_starts_with($title, 'Справочник');
    }

    /**
     * @return array{0: array<int, string>, 1: array<int, array<int, mixed>>}
     */
    private function readSheet($sheet): array
    {
        $highestCol = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());
        $highestRow = $sheet->getHighestDataRow();

        $parameters = [];
        for ($c = 1; $c <= $highestCol; $c++) {
            $label = trim((string) $sheet->getCell([$c, 2])->getValue());
            if ($label !== '') {
                $parameters[$c] = $label;
            }
        }

        $rows = [];
        for ($r = 5; $r <= $highestRow; $r++) {
            $row = [];
            $hasValue = false;

            foreach (array_keys($parameters) as $c) {
                $value = $sheet->getCell([$c, $r])->getValue();
                $row[$c] = $value;
                if ($value !== null && trim((string) $value) !== '') {
                    $hasValue = true;
                }
            }

            if ($hasValue) {
                $rows[] = $row;
            }
        }

        return [$parameters, $rows];
    }
}
