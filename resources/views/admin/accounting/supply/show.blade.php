@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $supply->created_at->format('d-m-y H:i') }}
            </h1>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        Добавить новые
        Список уже добавленных товаров, <br>
    </div>




@endsection
