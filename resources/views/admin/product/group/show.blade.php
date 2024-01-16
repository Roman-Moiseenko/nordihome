@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            {{ $group->name }}
        </h2>
    </div>
    <form action="{{ route('admin.product.group.add-product', $group) }}" method="POST">
        @csrf
        <div class="box flex p-5 items-center">

            <div class="image-fit w-10 h-10">
                <img class="rounded-full" src="{{ $group->getImage() }}" alt="{{ $group->name }}">
            </div>
            <div class="w-1/2 lg:w-1/4 mx-3">
                <x-searchProduct route="{{ route('admin.product.group.search', $group) }}" input-data="group-product" hidden-id="product_id"/>
            </div>
            <div>
                <x-base.button id="add-product" type="submit" variant="primary">Добавить товар в группу</x-base.button>
            </div>
        </div>
    </form>

    @foreach($group->products as $product)
        <div class="w-full mt-3 box p-5 flex items-center">
            <div class="image-fit w-10 h-10">
                <img class="rounded-full" src="{{ $product->getImage() }}" alt="{{ $product->name }}">
            </div>
            <div class="w-1/4 ml-4">
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
