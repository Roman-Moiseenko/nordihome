@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ $group->name }}
        </h2>
    </div>
    <div class="box flex p-5 items-center">
        <div class="image-fit w-10 h-10">
            <img class="rounded-full" src="{{ $group->getImage() }}" alt="{{ $group->name }}">
        </div>
        <form action="{{ route('admin.product.group.add-product', $group) }}" method="POST" class="flex ml-3">
            @csrf
            <div class="mr-3 w-100">
                <x-searchProduct route="{{ route('admin.product.group.search', $group) }}" input-data="group-product" hidden-id="product_id"/>
            </div>
            <div>
                <x-base.button id="add-product" type="submit" variant="primary">Найти и добавить товар в группу</x-base.button>
            </div>
        </form>
        <x-listCodeProducts route="{{ route('admin.product.group.add-products', $group) }}" caption-button="Добавить товары в группу" class="ml-3"/>
    </div>



    @foreach($group->products as $product)
        <div class="w-full mt-3 box p-5 flex items-center">
            <div class="image-fit w-10 h-10">
                <img class="rounded-full" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
            </div>
            <div class="w-32 text-center">
                {{ $product->code }}
            </div>
            <div class="w-1/4 font-medium">
                <a href="{{ route('admin.product.show', $product) }}">{{ $product->name }}</a>
            </div>
            <div class="w-auto text-slate-500 flex items-center mx-5">
                <x-base.lucide icon="external-link" class="w-4 h-4"/> {{ $product->category->getSlug() . '/' . $product->getSlug() }}
            </div>
            <div class="ml-auto w-40">
                <a class="flex items-center text-danger" href="#"
                   data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal"
                   data-route = {{ route('admin.product.group.del-product', ['group' => $group, 'product_id' => $product->id]) }}
                ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                    Delete </a>
            </div>
        </div>
    @endforeach


    {{ \App\Forms\ModalDelete::create('Вы уверены?',
     'Вы действительно хотите удалить атрибут?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
