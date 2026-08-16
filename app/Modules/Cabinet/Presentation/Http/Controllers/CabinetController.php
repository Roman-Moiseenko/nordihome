<?php


namespace App\Modules\Cabinet\Presentation\Http\Controllers;


use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Shop\Presentation\Http\Controllers\Web\ShopController;
use App\Modules\User\Service\UserService;
use Illuminate\Http\Request;
use function response;
use function view;

class CabinetController extends ShopController
{

    private UserService $service;

    public function __construct(UserService $service)
    {
        //parent::__construct();
        $this->service = $service;
    }

    public function view(Request $request)
    {
        return view('cabinet.view');
    }

    public function profile(Client $client)
    {
        //
    }

    public function update(Request $request, Client $client)
    {
        //
    }

    //AJAX
    //TODO Перенести в Livewire
    public function fullname(Client $client, Request $request)
    {
        $result = $this->service->setFullname($client, $request);
        $client->refresh();
        return response()->json($result);
    }

    public function phone(Client $client, Request $request)
    {
        $result = $this->service->setPhone($client, $request);
        $client->refresh();
        return response()->json($result);
    }

    public function email(Client $client, Request $request)
    {
        $result = $this->service->setEmail($client, $request);
        $user->refresh();
        return response()->json($result);
    }

    public function password(Client $user, Request $request)
    {
        $result = $this->service->setPassword($client, $request);
        $user->refresh();
        return response()->json($result);
    }
}
