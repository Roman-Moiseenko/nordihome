<?php


namespace App\Modules\Cabinet\Presentation\Http\Controllers;


use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Cabinet\Application\Queries\PageCabinetQuery;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopAbstractController;

use Illuminate\Http\Request;
use function view;

class CabinetController extends ShopAbstractController
{

    public function __construct(
        private readonly PageCabinetQuery $pageCabinetQuery,
    )
    {
    }

    public function view(Request $request)
    {
        $data = $this->pageCabinetQuery->execute($this->getClient($request));
        return view('cabinet.view', ['pageData' => $data]);
    }

    public function profile(Client $client)
    {
        //
    }

    public function update(Request $request, Client $client)
    {
        //
    }


    public function email(Client $client, Request $request)
    {
        //FixMe Сделать замену почты и верификации
        //  $result = $this->service->setEmail($client, $request);
        //   $client->refresh();
        //  return response()->json($result);
    }

    public function password(Client $client, Request $request)
    {
        //FixMe Сделать смены пароля
        //    $result = $this->service->setPassword($client, $request);
        //    $client->refresh();
        //     return response()->json($result);
    }
}
