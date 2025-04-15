<?php

namespace App\Modules\Nordihome\Service;

use Revolution\Google\Sheets\Facades\Sheets;

class GoogleSheetService
{

//    const string spreadsheetId = '19nzMeB8ffnZfo728k-f_Y_rYfePBSS85DOQi--PrSew'; // Test 1
    const string spreadsheetId = '1aGQXOFJwYT2z7MuqcKuM2ie2icIKPN7u0vnZOu7wwlY';
    const string sheetName = 'BLUM'; //Furniture
    const string col = 'H'; // D
   // const string user_key = '90822ced7cec397ef9cc95233910eebacff468d1';
    public function getFurnitureRows(): array
    {
        //dd(config('google.service'));
        $rows = Sheets::spreadsheet(self::spreadsheetId)->sheet(self::sheetName)->get();
        $header = $rows->pull(1);
        $values = Sheets::collection(header: $header, rows: $rows);
        return $values->toArray();
        //setAccessToken(self::user_key)->
//
    }

    public function setData(mixed $number, mixed $price1, mixed $price2): void
    {
        //$rows = Sheets::spreadsheet(self::spreadsheetId)->sheet('Furniture')->get();
        Sheets::spreadsheet(self::spreadsheetId)
            ->sheet(self::sheetName)
            ->range(self::col . $number)
            ->update([[$price1, $price2]]);
    //    Sheets::sheet('Furniture')->range('E' . $number)->update([[$price2]]);
    }
}
