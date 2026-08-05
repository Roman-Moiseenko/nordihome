<?php

namespace App\Livewire\Cabinet\Wish;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\User\Entity\User;
use Livewire\Attributes\On;
use Livewire\Component;

class WishPage extends Component
{

    private ?Client $client;
    public mixed $wishes;

    public function mount(int $clientId)
    {
        $this->client = Client::find($clientId);
        $this->refresh_data();
    }

    public function render()
    {
        return view('livewire.cabinet.wish.wish-page');
    }

    #[On('update-wish')]
    public function refresh_data()
    {
        $this->client->refresh();
        $this->wishes = $this->client->wishes;
    }

    public function remove()
    {
        $this->refresh_data();
    }
}
