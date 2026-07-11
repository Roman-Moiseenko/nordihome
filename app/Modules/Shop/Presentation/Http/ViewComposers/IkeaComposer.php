<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use App\Modules\Shop\Application\Queries\Ikea\GetIkeaTreeQuery;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\View\View;

class IkeaComposer
{
    public function __construct(
        private GetIkeaTreeQuery $query
    ) {}

    /**
     * @throws LockTimeoutException
     */
    public function compose(View $view): void
    {
        // Передаём дерево во все представления, которым нужен composer
        $view->with('ikeaTree', $this->query->execute());
    }
}
