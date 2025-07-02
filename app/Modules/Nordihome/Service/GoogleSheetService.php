<?php

namespace App\Modules\Nordihome\Service;

use Illuminate\Support\Facades\Log;
use Revolution\Google\Sheets\Facades\Sheets;

class GoogleSheetService
{

//    const string spreadsheetId = '19nzMeB8ffnZfo728k-f_Y_rYfePBSS85DOQi--PrSew'; // Test 1
    const string spreadsheetId = '1qg9_x9kz4iDDAUc14OyDXCGVbgMnC1mCrruYsV6-c3c'; //'1aGQXOFJwYT2z7MuqcKuM2ie2icIKPN7u0vnZOu7wwlY';
    //const string sheetName = 'BLUM'; //Furniture
   // const string col = 'H'; // D
   // const string user_key = '90822ced7cec397ef9cc95233910eebacff468d1';
    public function getFurnitureRows(string $sheetName): array
    {
        //dd(config('google.service'));
        $rows = Sheets::spreadsheet(self::spreadsheetId)->sheet($sheetName)->get();
        $header = $rows->pull(1);
        $values = Sheets::collection(header: $header, rows: $rows);
        return $values->toArray();
        //setAccessToken(self::user_key)->
//
    }

    public function setData(mixed $number, mixed $price1, mixed $price2, string $sheetName, string $col): void
    {
        Log::info("******** Парсим цены ФУРНИТУРЫ  *********");
        Log::info(json_encode([$sheetName, $col, $number, $price1, $price2]));
        Sheets::spreadsheet(self::spreadsheetId)
            ->sheet($sheetName)
            ->range(($col) . $number)
            ->update([[$price1, $price2]]);
    }
}
