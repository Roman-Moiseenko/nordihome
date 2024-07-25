<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class Video extends Component
{

    public Product $product;
    public string $url;
    public string $caption = '';
    public string $text = '';

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }

    #[On('update-video')]
    public function refresh_fields()
    {
        $this->product->refresh();
    }

    public function add()
    {
        if (empty($this->url)) throw new \DomainException('Не указана ссылка на видео');
        $this->product->videos()->save(
            \App\Modules\Base\Entity\Video::register($this->url, $this->caption, $this->text)
        );

        $this->url = '';
        $this->caption = '';
        $this->text = '';
    }

    public function render()
    {
        return view('livewire.admin.product.items.video');
    }

    public function exception($e, $stopPropagation)
    {
        if ($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка при добавлении видео', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
