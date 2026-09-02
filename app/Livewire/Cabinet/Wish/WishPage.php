<?php

namespace App\Livewire\Cabinet\Wish;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\Cabinet\Application\Actions\Wish\RemoveWishUseCase;
use App\Modules\Cabinet\Application\Queries\ListWishClientQuery;
use App\Modules\User\Entity\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class WishPage extends Component
{

    private ?Client $client;
    public mixed $wishes;
    private  ListWishClientQuery $listWishClientQuery;

    public function boot(ListWishClientQuery $listWishClientQuery): void
    {
        $this->listWishClientQuery = $listWishClientQuery;
        $this->client = (auth()->check() && auth()->user()->isClient()) ? auth()->user()->profileable : null;
    }

    public function mount()
    {
        $this->refresh_data();
    }

    public function render()
    {
        return view('livewire.cabinet.wish.wish-page');
    }

    #[On('update-wish')]
    public function refresh_data()
    {
        $items = $this->listWishClientQuery->execute($this->client->id);
        $this->wishes = Collection::make($items)->toArray();
    }

    public function remove()
    {
        $this->refresh_data();
    }
}
