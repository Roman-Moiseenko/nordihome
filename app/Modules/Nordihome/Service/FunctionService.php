<?php

namespace App\Modules\Nordihome\Service;

use App\Modules\Base\Entity\Package;
use App\Modules\Shop\Parser\ParserService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FunctionService
{

    private ParserService $parserService;

    public function __construct(ParserService $parserService)
    {
        $this->parserService = $parserService;
    }

    public function parser_dimensions(Request $request)
    {
        $textarea = $request->input('codes');
        $textarea = str_replace(".", "", $textarea);
        $codes = explode("\n", str_replace("\r", "", $textarea));
        $array = [];
        foreach ($codes as $code) {
            if (!empty(trim($code))) {
                $composites = [];
                $packages = null;
                try {
                    $parser_product = $this->parserService->parsingData($code);

                    $packages = $parser_product['packages'];

                    foreach ($parser_product['composite'] as $composite) {
                        $parser_composite = $this->parserService->parsingData($composite['code']);
                        $composites[$composite['code']] = ['packages' => $parser_composite['packages']];
                    }
                } catch (\DomainException $e) {
                    //   $packages[] = new Package();
                }
                $array[$code] = ['packages' => $packages, 'composites' => $composites];
            }
        }
        // dd($array);
        //TODO Записать $array в файл xlsx
        return $this->reportXLSX($array);
    }


    public function reportXLSX(array $array): string
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue([1, 1], 'Артикул');
        $activeWorksheet->setCellValue([2, 1], 'Высота');
        $activeWorksheet->setCellValue([3, 1], 'Ширина');
        $activeWorksheet->setCellValue([4, 1], 'Длина');
        $row = 1;
        foreach ($array as $key => $value) {
            $row++;
            $col = 1;
            $activeWorksheet->setCellValue([$col, $row], $this->parserService->toCode($key));
            // dd($value['packages']);
            $this->packagesToXLSX($activeWorksheet, $value['packages'], $col, $row);

            foreach ($value['composites'] as $key2 => $composite) {
                $col++;
                $activeWorksheet->setCellValue([$col, $row], $key2);
                $this->packagesToXLSX($activeWorksheet, $composite['packages'], $col, $row);
            }
        }


        $file = storage_path() . '/app/public/dimensions.xlsx';

        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!file_exists($path)) mkdir($path, 0777, true);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($file);
        return $file;
    }

    private function packagesToXLSX(&$activeWorksheet, $packages, &$col, $row): void
    {
        //391.246.22
        /** @var Package $package */
        if (!is_null($packages))
            foreach ($packages as $package) {
                $col++;
                $activeWorksheet->setCellValue([$col, $row], (string)$package->height);
                $col++;
                $activeWorksheet->setCellValue([$col, $row], (string)$package->width);
                $col++;
                $activeWorksheet->setCellValue([$col, $row], (string)$package->length);
            }
    }
}
