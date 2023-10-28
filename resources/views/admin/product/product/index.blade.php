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
@endsection
