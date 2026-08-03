<?php

namespace App\Modules\Shop\Presentation\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Modules\Shop\Application\DTOs\ClientContext;
use Illuminate\Http\Request;

class ShopController extends Controller
{

    public function getClient(Request $request): ClientContext
    {
        return $request->attributes->get('client_context') ?? new ClientContext();
    }
}
