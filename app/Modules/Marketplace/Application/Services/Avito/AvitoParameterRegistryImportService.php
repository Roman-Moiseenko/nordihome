<?php

namespace App\Modules\Marketplace\Application\Services\Avito;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterRegistryEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRegistryRepositoryInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Загружает русский и английский шаблоны Авито и наполняет реестр параметров.
 *
 * Сопоставление названий — по номеру колонки: из английского файла берётся
 * английское название (name_en), из русского — русское (name_ru). Если запись
 * с таким английским названием уже есть в реестре, поле пропускается.
 */
readonly class AvitoParameterRegistryImportService
{
    public function __construct(
        private AvitoParameterRegistryRepositoryInterface $registryRepository,
    ) {}

    public function import(string $ruPath, string $enPath): int
    {
        $ru = $this->load($ruPath);
        $en = $this->load($enPath);

        $ruSheet = $ru->getSheet(1);
        $enSheet = $en->getSheet(1);

        $ruHeaders = $this->readHeaders($ruSheet);
        $enHeaders = $this->readHeaders($enSheet);

        $created = 0;

        foreach ($enHeaders as $col => $nameEn) {
            $nameEn = trim((string) $nameEn);
            if ($nameEn === '') {
                continue;
            }

            // Уже есть английское название — пропускаем поле.
            if ($this->registryRepository->existsByNameEn($nameEn)) {
                continue;
            }

            $nameRu = trim((string) ($ruHeaders[$col] ?? ''));
            $type = $this->parseType((string) $ruSheet->getCell([$col, 4])->getValue());
            $isRequired = $this->parseRequired((string) $ruSheet->getCell([$col, 3])->getValue());

            $this->registryRepository->save(new AvitoParameterRegistryEntity(
                nameEn: $nameEn,
                nameRu: $nameRu !== '' ? $nameRu : null,
                type: $type,
                isRequired: $isRequired,
            ));

            $created++;
        }

        return $created;
    }

    private function load(string $path): Spreadsheet
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        return $reader->load($path);
    }

    /**
     * @return array<int, string> column => value
     */
    private function readHeaders($sheet): array
    {
        $headers = [];
        $maxCol = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        for ($c = 1; $c <= $maxCol; $c++) {
            $value = trim((string) $sheet->getCell([$c, 2])->getValue());
            if ($value !== '') {
                $headers[$c] = $value;
            }
        }

        return $headers;
    }

    private function parseRequired(string $value): bool
    {
        $value = trim($value);

        if (str_starts_with($value, 'Станет обязательным')) {
            return true;
        }

        return $value === 'Обязательный';
    }

    private function parseType(string $value): ?string
    {
        $value = trim($value);

        return match (true) {
            $value === 'Подробнее о параметре' => null,
            $value === 'Текст' => 'string',
            $value === 'Число' => 'integer',
            $value === 'Дробное число' => 'float',
            $value === 'Одно или несколько значений из выпадающего списка в ячейке' => 'array',
            $value === 'Одно значение из выпадающего списка в ячейке' => 'string',
            str_starts_with($value, 'Одно значение из листа') => 'string',
            default => null,
        };
    }
}
