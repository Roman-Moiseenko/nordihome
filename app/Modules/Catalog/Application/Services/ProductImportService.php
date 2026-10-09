<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\Services;

/**
 * Импорт товаров из XLSX-файла (предпросмотр позиций).
 */
class ProductImportService
{
    /**
     * @return array{products: array<int, array{code: string, quantity: float, price: float, price2: float}>}
     *         | array{error: string, products: array}
     */
    public function uploadByXlsx(?\Illuminate\Http\UploadedFile $file = null): array
    {
        try {
            set_time_limit(100);

            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            $spreadsheet = $reader->load($file->getPathName());
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            $rows = [];
            foreach ($sheetData as $row) {
                $rows[] = array_values(array_filter($row, fn($item) => $item !== null));
            }
            $rows = array_values(array_filter($rows));

            $products = [];
            foreach ($rows as $item) {
                $products[] = [
                    'code' => $item[0],
                    'quantity' => isset($item[1]) ? (float) $item[1] : 1,
                    'price' => isset($item[2]) ? (float) str_replace(' ', '', $item[2]) : 0,
                    'price2' => isset($item[3]) ? (float) str_replace(' ', '', $item[3]) : 0,
                ];
            }

            set_time_limit(30);

            return ['products' => $products];
        } catch (\Throwable $e) {
            return [
                'error' => $e->getMessage(),
                'products' => [],
            ];
        }
    }
}
