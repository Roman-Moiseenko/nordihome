@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Товары
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

            <button class="btn btn-primary shadow-md mr-2"
                    onclick="window.location.href='{{ route('admin.product.create') }}'">Создать товар
            </button>
        </div>
    </div>

    @foreach($products as $product)
        <div>
            <p>{{ $product->id }}</p>
            <p>{{ $product->name }}</p>
            @foreach($product->prod_attributes as $attribute)
                <div>
                    <p>{{ $attribute->name }}</p>
                    <p> {{ $attribute->ValueJSON() }}</p>
                </div>
            @endforeach

        </div>

    @endforeach

    <x-base.preview>
        <x-base.dropzone
            class="dropzone"
            multiple
            action="/file-upload"
        >
            <div class="text-lg font-medium">
                Drop files here or click to upload.
            </div>
            <div class="text-gray-600">
                This is just a demo dropzone. Selected files are
                <span class="font-medium">not</span> actually
                uploaded.
            </div>
        </x-base.dropzone>
    </x-base.preview>
@endsection
