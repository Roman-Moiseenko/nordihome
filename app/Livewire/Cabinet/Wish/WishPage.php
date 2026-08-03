<?php

namespace App\Livewire\Cabinet\Wish;

use App\Modules\Auth\Infrastructure\Models\Client;
use App\Modules\User\Entity\User;
use Livewire\Attributes\On;
use Livewire\Component;

class WishPage extends Component
{

    public ?Client $client;
    public mixed $wishes;

    public function mount(mixed $client)
    {
        $this->client = $client;
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
    }

    public function remove()
    {
        $this->refresh_data();
    }
}
