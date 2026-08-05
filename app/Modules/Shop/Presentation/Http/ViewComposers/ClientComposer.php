<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use App\Modules\Shop\Application\Queries\Client\GetInfoClientQuery;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
class ClientComposer
{

    public function __construct(private readonly GetInfoClientQuery $getClientQuery)
    {
    }

    public function compose(View $view): void
    {
        $client = (auth()->check() && auth()->user()->isClient())
            ? $this->getClientQuery->execute(auth()->user()->profileable_id)
            : null;

        $view->with('client', $client);
    }
}
