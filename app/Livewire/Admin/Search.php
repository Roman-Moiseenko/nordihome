<?php

namespace App\Livewire\Admin;


use App\Modules\Product\Repository\ProductRepository;
use Livewire\Attributes\On;
use Livewire\Component;

class Search extends Component
{

    //TODO Разработать Компонент
    public string $search = '';
    public array $products = [];
    private ProductRepository $repository;

    public bool $quantity;
    public bool $duplicate;
    public bool $parser;
    public mixed $document;
    public string $routeAdd;
    public string $comment = '*';
    public int $_quantity;


    public function boot(ProductRepository $repository)
    {
        $this->repository = $repository;
    }


    public function mount(bool $quantity, string $routeAdd, bool $duplicate = false, bool $parser = false, $document = null)
    {
        $this->quantity = $quantity;
        $this->duplicate = $duplicate;
        $this->parser = $parser;
        $this->routeAdd = $routeAdd;
        $this->document = $document;
    }

    #[On('search-product')]
    public function find_products($search)
    {
        $products = $this->repository->search($search);
        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'name' => $product->name,
                'id' => $product->id,
                'code' => $product->code,
                'code_search' => $product->code_search,
                ];
        }

        $this->dispatch('update-tom-select', data: json_encode($data));
    }

    public function select()
    {
        //$this->products = [];
        throw new \DomainException($this->search);
    }


    public function render()
    {
        return view('livewire.admin.search');
    }

    public function add()
    {

    }

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в товаре', message: $e->getMessage());
            $stopPropagation();
        }
    }
}
