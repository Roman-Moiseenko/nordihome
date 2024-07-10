@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Товары приоритетного показа
        </h2>
    </div>
    <div class="box flex p-5 items-center">
        <x-searchAddProduct route-save="{{ route('admin.product.priority.add-product') }}" width="100"
                            published="1" caption="Добавить товар"/>
        <x-listCodeProducts route="{{ route('admin.product.priority.add-products') }}" caption-button="Добавить товары" class="ml-3"/>
    </div>

    @foreach($products as $product)
        <div class="w-full mt-3 box p-5 flex items-center">
            <div class="image-fit w-10 h-10">
                <img class="rounded-full" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
            </div>
            <div class="w-32 text-center">
                {{ $product->code }}
            </div>
            <div class="w-1/4 font-medium">
                <a href="{{ route('admin.product.edit', $product) }}">{{ $product->name }}</a>
            </div>
            <div class="w-auto text-slate-500 flex items-center mx-5">
                <x-base.lucide icon="file-box" class="w-4 h-4"/> {{ $product->category->name }}
            </div>
            <div class="ml-auto w-40">
                <a class="flex items-center text-danger" href="#"
                   data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal"
                   data-route = {{ route('admin.product.priority.del-product', $product) }}
                ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                    Delete </a>
            </div>
        </div>
    @endforeach


    {{ \App\Forms\ModalDelete::create('Вы уверены?',
     'Вы действительно хотите удалить товар из приоритетного показа?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
