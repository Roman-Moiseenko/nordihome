<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use App\Modules\Auth\Application\Queries\GetInfoWebClientQuery;
use Illuminate\View\View;

readonly class ClientComposer
{

    public function __construct(private GetInfoWebClientQuery $getClientQuery)
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
