<?php

namespace App\Modules\Marketplace\Application\Services\Avito;

use App\Modules\Marketplace\Domain\Entities\AvitoParameterEntity;
use App\Modules\Marketplace\Domain\Entities\AvitoParameterRegistryEntity;
use App\Modules\Marketplace\Domain\Exceptions\AvitoFieldNotInRegistryException;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryFieldRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoCategoryRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRegistryRepositoryInterface;
use App\Modules\Marketplace\Domain\Interfaces\AvitoParameterRepositoryInterface;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Загрузка структуры категории из английского шаблона Авито.
 *
 * Каждый столбец сверяется с реестром параметров по английскому названию.
 * Если поле отсутствует в реестре — категория не загружается и выбрасывается
 * AvitoFieldNotInRegistryException с перечнем недостающих полей.
 *
 * По виду (kind) параметра:
 *  - product/skip — пропускается (это поля товара или неиспользуемые поля);
 *  - common — проверяется/создаётся AvitoParameter (варианты добавляются сразу);
 *  - category (или kind не задан) — грузится в AvitoCategoryFieldEntity.
 */
readonly class AvitoCategoryStructureImportService
{
    public function __construct(
        private AvitoCategoryRepositoryInterface $categoryRepository,
        private AvitoCategoryFieldRepositoryInterface $fieldRepository,
        private AvitoParameterRegistryRepositoryInterface $registryRepository,
        private AvitoParameterRepositoryInterface $parameterRepository,
    ) {}

    public function import(string $filePath): void
    {
        $spreadsheet = $this->load($filePath);
        $sheet = $spreadsheet->getSheet(1);
        $dict = $spreadsheet->getSheet(2) ?? null;

        $path = trim((string) $sheet->getCell([1, 1])->getValue());
        if ($path === '') {
            return;
        }

        $names = array_values(array_filter(array_map('trim', explode(' - ', $path))));
        if (empty($names)) {
            return;
        }

        $highestCol = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        // Сначала сверяем все столбцы с реестром, чтобы не создавать категорию,
        // если какого-то поля нет в реестре.
        $missing = [];
        $columns = [];

        for ($c = 1; $c <= $highestCol; $c++) {
            $nameEn = trim((string) $sheet->getCell([$c, 2])->getValue());
            if ($nameEn === '') {
                continue;
            }

            $registry = $this->registryRepository->getByNameEn($nameEn);
            if ($registry === null) {
                $missing[] = $nameEn;
                continue;
            }

            $columns[$c] = $registry;
        }

        if (!empty($missing)) {
            throw new AvitoFieldNotInRegistryException($missing);
        }

        $category = $this->categoryRepository->findOrCreateByNames($names);
        $fields = [];

        foreach ($columns as $c => $registry) {
            if ($registry->kind === AvitoParameterRegistryEntity::KIND_PRODUCT
                || $registry->kind === AvitoParameterRegistryEntity::KIND_SKIP) {
                continue;
            }

            $rawType = (string) $sheet->getCell([$c, 4])->getValue();
            $variants = $this->extractVariants($dict, $c, $rawType);

            if ($registry->kind === AvitoParameterRegistryEntity::KIND_COMMON) {
                $this->ensureCommonParameter($registry, $variants);
                continue;
            }

            // kind = category или не задан — параметр категории.
            $fields[] = [
                'tag' => Str::slug($registry->nameEn),
                'name' => $registry->nameRu ?? $registry->nameEn,
                'type' => $registry->type,
                'is_required' => $registry->isRequired,
                'allowed_values' => empty($variants) ? null : $variants,
                'depends_on' => null,
            ];
        }

        if (!empty($fields)) {
            $this->fieldRepository->upsertForCategory($category->id, $fields);
        }

        $this->categoryRepository->fixTree();
    }

    private function ensureCommonParameter(AvitoParameterRegistryEntity $registry, array $variants): void
    {
        if ($this->parameterRepository->getByNameEn($registry->nameEn) !== null) {
            return;
        }

        $this->parameterRepository->save(new AvitoParameterEntity(
            name: $registry->nameRu ?? $registry->nameEn,
            slug: Str::slug($registry->nameEn),
            nameEn: $registry->nameEn,
            variants: $variants,
            isHtml: false,
        ));
    }

    private function load(string $path): Spreadsheet
    {
        try {
            return IOFactory::load($path);
        } catch (\ErrorException $e) {
            if (!str_contains($e->getMessage(), 'Undefined array key')) {
                throw $e;
            }

            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);

            return $reader->load($path);
        }
    }

    /**
     * @return string[]
     */
    private function extractVariants(?Worksheet $dict, int $col, string $rawType): array
    {
        $type = trim($rawType);

        $hasVariants = $type === 'Одно значение из выпадающего списка в ячейке'
            || $type === 'Одно или несколько значений из выпадающего списка в ячейке';

        if (!$hasVariants || $dict === null) {
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
}
