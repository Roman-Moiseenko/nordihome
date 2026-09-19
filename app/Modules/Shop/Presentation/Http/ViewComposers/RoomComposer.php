<?php

namespace App\Modules\Shop\Presentation\Http\ViewComposers;

use App\Modules\Shop\Application\Queries\Room\GetRoomTreeQuery;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\View\View;

class RoomComposer
{
    public function __construct(
        private GetRoomTreeQuery $query
    ) {}

    /**
     * @throws LockTimeoutException
     */
    public function compose(View $view): void
    {
        $view->with('roomTree', $this->query->execute());
    }
}
