<?php

namespace App\Livewire\Admin\Product\Items;

use Livewire\Component;

class VideoItem extends Component
{

    public string $url;
    public string $caption;
    public string $text;
    public \App\Modules\Base\Entity\Video $video;

    public function mount(\App\Modules\Base\Entity\Video $video)
    {
        $this->video = $video;
        $this->refresh_fields();
    }

    public function refresh_fields()
    {
        $this->url = $this->video->url;
        $this->caption = $this->video->caption;
        $this->text = $this->video->description;
    }

    public function save()
    {
        $this->video->url = $this->url;
        $this->video->caption = $this->caption;
        $this->video->description = $this->text;
        $this->video->save();
    }

    public function remove()
    {
        $this->video->delete();
        $this->dispatch('update-video');
    }
    public function render()
    {
        return view('livewire.admin.product.items.video-item');
    }
}
