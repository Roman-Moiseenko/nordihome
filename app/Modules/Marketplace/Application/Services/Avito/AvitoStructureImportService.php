<?php

namespace App\Modules\Marketplace\Application\Services\Avito;

use App\Modules\Marketplace\Application\Mappers\AvitoProductFieldMap;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryRepositoryInterface;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AvitoStructureImportService
{
    public function __construct(
        private readonly AvitoCategoryRepositoryInterface $categoryRepository,
        private readonly AvitoCategoryFieldRepositoryInterface $fieldRepository,
    ) {}

    public function import(string $filePath): void
    {
        $spreadsheet = $this->loadSpreadsheet($filePath);
        $names = $spreadsheet->getSheetNames();

        // Шаблон Авито: "Инструкция", "Объявления", "Справочники".
        $single = count($names) <= 3;

        if ($single) {
            $dict = $spreadsheet->getSheet(2) ?? null;
            $this->processListingSheet($spreadsheet, 1, $dict, false);
        } else {
            for ($i = 1; $i < count($names); $i++) {
                $sheet = $spreadsheet->getSheet($i);
                if ($sheet->getSheetState() !== Worksheet::SHEETSTATE_VISIBLE) {
                    continue;
                }

                $dict = ($i + 1 < count($names)) ? $spreadsheet->getSheet($i + 1) : null;
                $this->processListingSheet($spreadsheet, $i, $dict, true);
            }
        }

        $this->categoryRepository->fixTree();
    }

    private function loadSpreadsheet(string $filePath): Spreadsheet
    {
        try {
            return IOFactory::load($filePath);
        } catch (\ErrorException $e) {
            // PhpSpreadsheet 3.x: абсолютный диапазон валидации на весь столбец
            // (например '$D$5:$D$1048576') падает в Coordinate::columnIndexFromString().
            // Такие валидации нужны только для извлечения вариантов в выгрузках
            // «АвитоЗагрузки», поэтому откатываемся на чтение только данных.
            if (!str_contains($e->getMessage(), 'Undefined array key')) {
                throw $e;
            }

            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);

            return $reader->load($filePath);
        }
    }

    private function processListingSheet(
        Spreadsheet $spreadsheet,
        int $index,
        ?Worksheet $dict,
        bool $multi
    ): void {
        $sheet = $spreadsheet->getSheet($index);

        // Полный путь категории в ячейке A1.
        $path = trim((string) $sheet->getCell([1, 1])->getValue());
        if ($path === '') {
            return;
        }

        $names = array_values(array_filter(array_map('trim', explode(' - ', $path))));
        if (empty($names)) {
            return;
        }

        $category = $this->categoryRepository->findOrCreateByNames($names);

        $highestCol = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());
        $fields = [];

        for ($c = 1; $c <= $highestCol; $c++) {
            $label = trim((string) $sheet->getCell([$c, 2])->getValue());
            if ($label === '') {
                continue;
            }

            // Общие поля товара Авито и поля без соответствия в сущности —
            // не являются параметрами категории.
            if (AvitoProductFieldMap::isMappedLabel($label) || AvitoProductFieldMap::shouldSkip($label)) {
                continue;
            }

            $isRequired = $this->parseRequired((string) $sheet->getCell([$c, 3])->getValue());
            $rawType = (string) $sheet->getCell([$c, 4])->getValue();
            $type = $this->parseType($rawType);

            $allowedValues = $this->extractVariants($sheet, $dict, $c, $rawType, $multi, $spreadsheet);

            $fields[] = [
                'tag' => Str::slug($label),
                'name' => $label,
                'type' => $type,
                'is_required' => $isRequired,
                'allowed_values' => empty($allowedValues) ? null : $allowedValues,
                'depends_on' => null,
            ];
        }

        if (!empty($fields)) {
            $this->fieldRepository->upsertForCategory($category->id, $fields);
        }
    }

    private function parseRequired(string $value): bool
    {
        $value = trim($value);

        // "Станет обязательным <дата>" — значит Обязательный.
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

    /**
     * @return string[]
     */
    private function extractVariants(
        Worksheet $sheet,
        ?Worksheet $dict,
        int $col,
        string $rawType,
        bool $multi,
        Spreadsheet $spreadsheet
    ): array {
        $type = trim($rawType);

        $hasVariants = $type === 'Одно значение из выпадающего списка в ячейке'
            || $type === 'Одно или несколько значений из выпадающего списка в ячейке';

        if (!$hasVariants) {
            return [];
        }

        // Шаблон Авито: варианты в справочнике под теми же номерами столбцов, со 2-й строки.
        if (!$multi) {
            return $this->readColumnVariants($dict, $col);
        }

        // Выгрузка АвитоЗагрузки: варианты по формуле валидации на скрытом листе.
        return $this->readValidationVariants($sheet, $col, $spreadsheet);
    }

    /**
     * @return string[]
     */
    private function readColumnVariants(?Worksheet $dict, int $col): array
    {
        if ($dict === null) {
            return [];
        }

        $values = [];
        $maxRow = $dict->getHighestDataRow();

        for ($r = 2; $r <= $maxRow; $r++) {
            $v = $dict->getCell([$col, $r])->getValue();
            if ($v !== null && $v !== '') {
                $values[] = trim((string) $v);
            }
        }

        return array_values(array_unique(array_filter($values)));
    }

    /**
     * @return string[]
     */
    private function readValidationVariants(Worksheet $sheet, int $col, Spreadsheet $spreadsheet): array
    {
        $colLetter = Coordinate::stringFromColumnIndex($col);

        foreach ($sheet->getDataValidationCollection() as $cell => $dv) {
            $letter = str_replace('$', '', preg_replace('/[0-9]/', '', (string) $cell));
            if ($letter !== $colLetter) {
                continue;
            }

            $formula = (string) $dv->getFormula1();
            if ($formula === '' || !str_contains($formula, '!')) {
                continue;
            }

            [$sheetPart, $range] = explode('!', $formula, 2);
            $sheetName = trim($sheetPart, "'");
            $range = str_replace('$', '', $range);

            $dict = $spreadsheet->getSheetByName($sheetName);
            if ($dict === null) {
                continue;
            }

            $values = [];
            foreach ($dict->rangeToArray($range, null, true, false) as $row) {
                foreach ($row as $v) {
                    if ($v !== null && $v !== '') {
                        $values[] = trim((string) $v);
                    }
                }
            }

            return array_values(array_unique(array_filter($values)));
        }

        return [];
    }
}
