<?php

namespace App\Livewire\Admin;


use App\Modules\Product\Repository\ProductRepository;
use Livewire\Component;

class Search extends Component
{

    //TODO Разработать Компонент
    public string $search = '';
    public $products = [];
    private ProductRepository $repository;

    public function boot(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function find_products()
    {
        $this->products = $this->repository->search($this->search);

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

    public function exception($e, $stopPropagation) {
        if($e instanceof \DomainException) {
            $this->dispatch('window-notify', title: 'Ошибка в товаре', message: $e->getMessage());
            $stopPropagation();

        }
    }
}
