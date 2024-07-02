<?php

namespace App\Livewire\Admin\Product\Items;

use App\Modules\Product\Entity\Product;
use App\Modules\Product\Entity\Tag;
use App\Modules\Product\Service\TagService;
use Livewire\Component;

class Description extends Component
{

    public Product $product;

    public string $description;
    public string $short;
    public array $_tags = [];
    public mixed $tags;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->refresh_fields();
    }


    public function refresh_fields()
    {
        $this->product->refresh();
        $this->description = $this->product->description;
        $this->short = $this->product->short;
        $this->tags = Tag::orderBy('name')->get();
    }

    public function updated($property)
    {
        if ($property == 'description') {
            $this->product->description = $this->description;
            $this->product->save();
            $this->product->refresh();
        }
        if ($property == 'short')  {
            $this->product->short = $this->short;
            $this->product->save();
            $this->product->refresh();
        }
        if ($property == '_tags') {
          //  $this->dispatch('window-notify', title: 'Теги', message: json_encode($this->_tags));
        }
    }

    public function dehydrate() {
        //throw new \DomainException('***');

        $this->dispatch('initializeCkEditor');
    }

    public function save()
    {
        //$this->product->update();

        $tagService = new TagService();
        $this->product->tags()->detach();


        foreach ($this->_tags as $tag_id) {
            if (!is_null(Tag::find($tag_id))) {
                $this->product->tags()->attach((int)$tag_id);
            } else {
                $tag = $tagService->create($tag_id);
                $this->product->tags()->attach($tag->id);
            }
        }
        //$this->tags($request, $product);
        $this->refresh_fields();

        //$this->dispatch('window-notify', title: 'Теги', message: json_encode($this->_tags));
    }

    public function render()
    {
        return view('livewire.admin.product.items.description');
    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка', message: $e->getMessage());
            $stopPropagation();
            $this->refresh_fields();
        }
    }
}
