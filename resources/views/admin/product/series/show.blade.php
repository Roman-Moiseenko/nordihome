@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            {{ $series->name }}
        </h2>
    </div>
    <div class="box flex p-5 items-center">
        <x-searchAddProduct route-save="{{ route('admin.product.series.add-product', $series) }}" width="100"
                            published="1" caption="Добавить товар в серию"/>
        <x-listCodeProducts route="{{ route('admin.product.series.add-products', $series) }}" caption-button="Добавить товары в серию" class="ml-3"/>
    </div>

    @foreach($series->products as $product)
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
                   data-route = {{ route('admin.product.series.del-product', ['series' => $series, 'product_id' => $product->id]) }}
                ><x-base.lucide icon="trash-2" class="w-4 h-4"/>
                    Delete </a>
            </div>
        </div>
    @endforeach


    {{ \App\Forms\ModalDelete::create('Вы уверены?',
     'Вы действительно хотите удалить товар из серии?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
