<?php

declare(strict_types=1);

namespace App\Modules\Marketplace\Application\Services\Avito;

use App\Modules\Catalog\Domain\Interfaces\ProductRepositoryInterface;
use App\Modules\Marketplace\Application\DTOs\AvitoProduct\AvitoProductImportCreateData;
use App\Modules\Marketplace\Application\Mappers\AvitoProductFieldMap;
use App\Modules\Marketplace\Domain\Entities\AvitoCategoryEntity;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRepositoryInterface;

/**
 * Распознавание параметров одного листа (категории) выгрузки Авито.
 *
 * Поиск параметров идёт по русскому названию колонки:
 *  1) поле AvitoProduct (AvitoProductFieldMap::MAP) — пишется в сущность;
 *  2) общий параметр AvitoParameter — пропускается;
 *  3) параметр категории AvitoCategoryField — сохраняется в avito_product_values.
 *
 * На этом этапе формируется DTO создания объявления со всеми значениями,
 * но обращения к записи в БД не происходит (запись — в Job создания).
 */
readonly class AvitoProductImportService
{
    public function __construct(
        private AvitoParameterRepositoryInterface     $parameterRepository,
        private AvitoCategoryFieldRepositoryInterface $fieldRepository,
        private ProductRepositoryInterface            $productRepository,
        private AvitoProductDescriptionParser         $descriptionParser,
        private AvitoProductImportLogger              $logger,
    ) {}

    /**
     * @param string $categoryPath полный путь категории (для лога)
     * @param array<int, string> $parameters название колонки по её индексу
     * @param array<int, array<int, mixed>> $rows строки товаров (значение по индексу колонки)
     * @return AvitoProductImportCreateData[]
     */
    public function recognize(
        string $categoryPath,
        AvitoCategoryEntity $category,
        string $sheet,
        array $parameters,
        array $rows,
    ): array {
        $common = [];
        foreach ($this->parameterRepository->getAll() as $parameter) {
            $common[$parameter->name] = true;
        }

        $categoryFields = [];
        foreach ($this->fieldRepository->getByCategoryId($category->id) as $field) {
            $categoryFields[$field['name']] = $field;
        }

        // Распознавание колонок один раз на лист.
        $productColumns = []; // col => имя свойства сущности
        $fieldColumns = [];   // col => параметр категории
        $ignored = [];

        foreach ($parameters as $col => $label) {
            $label = trim((string) $label);
            if ($label === '' || AvitoProductFieldMap::isSkip($label)) {
                $ignored[$col] = true;
                continue;
            }

            $field = AvitoProductFieldMap::fieldByLabel($label);
            if ($field !== null) {
                $productColumns[$col] = $field;
                continue;
            }

            if (isset($common[$label])) {
                $ignored[$col] = true;
                continue;
            }

            if (isset($categoryFields[$label])) {
                $fieldColumns[$col] = $categoryFields[$label];
                continue;
            }

            $this->logger->parameterNotFound($sheet, $categoryPath, $label);
            $ignored[$col] = true;
        }

        $result = [];

        foreach ($rows as $row) {
            $result[] = $this->buildDto($categoryPath, $category, $sheet, $productColumns, $fieldColumns, $row);
        }

        return $result;
    }

    /**
     * @param array<int, string> $productColumns
     * @param array<int, array{id: int, name: string, type: ?string, allowed_values: ?array}> $fieldColumns
     * @param array<int, mixed> $row
     */
    private function buildDto(
        string $categoryPath,
        AvitoCategoryEntity $category,
        string $sheet,
        array $productColumns,
        array $fieldColumns,
        array $row,
    ): AvitoProductImportCreateData {
        $name = $this->stringValue($row, $productColumns, 'name') ?: 'Объявление';
        $description = null;
        $productId = null;

        $rawDescription = $this->stringValue($row, $productColumns, 'description');
        if ($rawDescription !== null) {
            $parsed = $this->descriptionParser->parse($rawDescription);
            $description = $parsed['description'];
            if ($parsed['article'] !== null) {
                $product = $this->productRepository->findByCode($parsed['article']);
                $productId = $product?->id;
            }
        }

        $sellPrice = $this->intValue($row, $productColumns, 'sellPrice', $sheet, $categoryPath, 'Цена');
        $avitoImages = $this->stringValue($row, $productColumns, 'avitoImages');
        $tnVed = $this->codeValue($row, $productColumns, 'tnVed');
        $okpd2 = $this->codeValue($row, $productColumns, 'okpd2');
        $country = $this->stringValue($row, $productColumns, 'country') ?: 'Россия';
        $delivery = $this->parseDelivery($this->stringValue($row, $productColumns, 'delivery'));

        $parameters = $this->buildParameters($sheet, $categoryPath, $fieldColumns, $row);

        return new AvitoProductImportCreateData(
            name: $name,
            categoryId: $category->id,
            description: $description,
            sellPrice: $sellPrice,
            avitoImages: $avitoImages,
            tnVed: $tnVed,
            okpd2: $okpd2,
            country: $country,
            delivery: $delivery,
            productId: $productId,
            parameters: $parameters,
        );
    }

    /**
     * @param array<int, array{id: int, name: string, type: ?string, allowed_values: ?array}> $fieldColumns
     * @param array<int, mixed> $row
     * @return array<int, array{avito_category_field_id: int, value: ?string}>
     */
    private function buildParameters(string $sheet, string $categoryPath, array $fieldColumns, array $row): array
    {
        $parameters = [];

        foreach ($fieldColumns as $col => $field) {
            $raw = $this->rawValue($row, $col);
            if ($raw === null || $raw === '') {
                continue;
            }

            $type = $field['type'] ?? null;

            if ($type === 'integer' || $type === 'float') {
                if (!is_numeric(str_replace([',', ' '], ['.', ''], $raw))) {
                    $this->logger->valueError($sheet, $categoryPath, $field['name'], (string) $raw, 'неверный числовой формат');
                    continue;
                }
                $value = (string) $raw;
            } elseif ($type === 'array') {
                $parts = array_map('trim', explode('|', (string) $raw));
                $parts = array_values(array_filter($parts, fn(string $v) => $v !== ''));
                $value = $parts === [] ? null : json_encode($parts, JSON_UNESCAPED_UNICODE);
            } else {
                $value = trim((string) $raw);
            }

            if ($value === null || $value === '') {
                continue;
            }

            $parameters[] = [
                'avito_category_field_id' => (int) $field['id'],
                'value' => $value,
            ];
        }

        return $parameters;
    }

    /**
     * @param array<int, string> $productColumns
     */
    private function stringValue(array $row, array $productColumns, string $field): ?string
    {
        foreach ($productColumns as $col => $f) {
            if ($f === $field) {
                $raw = $this->rawValue($row, $col);

                return $raw === null ? null : trim((string) $raw);
            }
        }

        return null;
    }

    /**
     * @param array<int, string> $productColumns
     */
    private function intValue(array $row, array $productColumns, string $field, string $sheet, string $categoryPath, string $label): ?int
    {
        $raw = $this->stringValue($row, $productColumns, $field);
        if ($raw === null || $raw === '') {
            return null;
        }

        $normalized = str_replace([' ', ' '], '', $raw);
        $normalized = str_replace(',', '.', $normalized);

        if (!is_numeric($normalized)) {
            $this->logger->valueError($sheet, $categoryPath, $label, $raw, 'неверный числовой формат');

            return null;
        }

        return (int) round((float) $normalized);
    }

    /**
     * Из строки вида «3924 90 000 9 — Посуда столовая…» берём только код
     * (часть до первого тире-разделителя), описание отбрасываем.
     *
     * @param array<int, string> $productColumns
     */
    private function codeValue(array $row, array $productColumns, string $field): ?string
    {
        $raw = $this->stringValue($row, $productColumns, $field);
        if ($raw === null || $raw === '') {
            return null;
        }

        $parts = preg_split('/\s*—\s*|\s*–\s*|\s+-\s+/u', $raw, 2);
        $code = trim($parts[0] ?? $raw);

        return $code === '' ? null : $code;
    }

    private function parseDelivery(?string $value): bool
    {
        $value = $value !== null ? trim($value) : '';

        if ($value === '' || mb_strtolower($value) === 'выключена') {
            return false;
        }

        return true;
    }

    private function rawValue(array $row, int $col): ?string
    {
        if (!array_key_exists($col, $row)) {
            return null;
        }

        $value = $row[$col];
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
