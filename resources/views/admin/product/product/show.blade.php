@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $product->name }}
            </h1>
        </div>
    </div>
    <div class="font-medium text-xl text-danger mt-6">
        В разработке. После торгового учета добавится движение товара, продажи и другие отчеты

    </div>
@endsection
