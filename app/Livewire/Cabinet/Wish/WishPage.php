<?php

namespace App\Livewire\Cabinet\Wish;

use App\Modules\User\Entity\User;
use Livewire\Attributes\On;
use Livewire\Component;

class WishPage extends Component
{

    public ?User $user;
    public mixed $wishes;

    public function mount(mixed $user)
    {
        $this->user = $user;
        $this->refresh_data();
    }

    public function render()
    {
        return view('livewire.cabinet.wish.wish-page');
    }

    #[On('update-wish')]
    public function refresh_data()
    {
        $this->user->refresh();
    }

    public function remove()
    {
        $this->refresh_data();
    }
}
