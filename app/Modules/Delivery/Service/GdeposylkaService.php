<?php
declare(strict_types=1);

namespace App\Modules\Delivery\Service;

use App\Modules\Delivery\Entity\DeliveryCargo;
use Illuminate\Support\Facades\Http;

class GdeposylkaService
{

    public function findTrack(string $track_number): int
    {

        return DeliveryCargo::STATUS_ISSUED;
        //TODO На тестовом подключить API https://gdeposylka.ru/

        $result = Http::withHeaders([
            'X-Authorization-Token' => env('GDEPOSYLKA_TOKEN', null),
            'Content-Type' => ' application/json'
        ])->get('https://gdeposylka.ru/api/v4/tracker/usps/' . $track_number)->json();

        //Проверка $result

    }
}
