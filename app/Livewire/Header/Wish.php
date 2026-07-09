<?php

namespace App\Livewire\Header;

use App\Modules\Auth\Infrastructure\Models\Client;
use Livewire\Attributes\On;
use Livewire\Component;

class Wish extends Component
{

    public ?Client $client;
    public int $count = 0;
    public array $items = [];

    public function mount()
    {

        $this->client = (auth()->check() && auth()->user()->isClient()) ? auth()->user()->profileable : null;

        $this->refresh_fields();
    }

    #[ On('update-header-wish')]
    public function refresh_fields()
    {
        if (!is_null($this->client))
        $this->items = array_map(function (\App\Modules\User\Entity\Wish $wish) {
            return [
                'id' => $wish->id,
                'image' => $wish->product->getImage('thumb'),
                'name' => $wish->product->name,
                'url' => route('shop.product.view', $wish->product->slug),
            ];
        },  $this->client->wishes()->getModels());

        $this->count = count($this->items);

    }

    public function remove($id)
    {
        $wish = \App\Modules\User\Entity\Wish::find($id);
        $this->dispatch('update-wish', product_id: $wish->product_id);
        $wish->delete();
        $this->refresh_fields();

    }

    public function remove_all()
    {

        foreach ($this->client->wishes as $wish) {
            $wish->delete();
        }
        $this->items = [];
        $this->count = 0;
        $this->dispatch('update-wish');
    }

    public function render()
    {
        return view('livewire.header.wish');

    }
}
