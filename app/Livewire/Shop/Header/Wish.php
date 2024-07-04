<?php

namespace App\Livewire\Shop\Header;

use App\Modules\User\Entity\User;
use Livewire\Attributes\On;
use Livewire\Component;

class Wish extends Component
{

    public ?User $user;
    public int $count = 0;
    public array $items = [];

    public function mount($user)
    {
        $this->user = $user;
        $this->refresh_fields();
    }

    #[On('update-header-wish')]
    public function refresh_fields()
    {
        if (!is_null($this->user))
        $this->items = array_map(function (\App\Modules\User\Entity\Wish $wish) {
            return [
                'id' => $wish->id,
                'image' => $wish->product->getImage('thumb'),
                'name' => $wish->product->name,
                'url' => route('shop.product.view', $wish->product->slug),
            ];
        },  $this->user->wishes()->getModels());

        $this->count = count($this->items);
    }

    public function remove($id)
    {
        /** @var \App\Modules\User\Entity\Wish $wish */
        $wish = \App\Modules\User\Entity\Wish::find($id);
        $this->dispatch('update-wish', product_id: $wish->product_id);
        $wish->delete();
        $this->refresh_fields();
    }

    public function remove_all()
    {
        foreach ($this->user->wishes as $wish) {
            $wish->delete();
        }
        $this->items = [];
        $this->count = 0;
        $this->dispatch('update-wish');
    }

    public function render()
    {
        return view('livewire.shop.header.wish');
    }
}
