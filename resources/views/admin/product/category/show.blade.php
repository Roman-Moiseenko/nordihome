@extends('layouts.side-menu')

@section('subcontent')
<div>
    <div class="intro-y flex items-center mt-8">
        <h1 class="text-lg font-medium mr-auto">
            {{ $category->name }}
        </h1>
    </div>
</div>
<div class="intro-y box px-5 pt-5 mt-5">
    <ul class="nav nav-link-tabs flex-col sm:flex-row justify-center lg:justify-start text-center py-5">
        <li class="nav-item">
            <a class="btn btn-primary py-1 px-2 mr-2"
               href="{{ route('admin.product.category.edit', $category) }}">Редактировать
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="btn btn-outline-secondary py-1 px-2"
               data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal" data-route = {{ route('admin.product.category.destroy', $category) }}>
                <i data-lucide="trash-2" width="24" height="24" class="lucide lucide-key-round w-4 h-4 mr-2"></i>
                Удалить </a>
        </li>
    </ul>
</div>

<div class="intro-y box px-5 py-5 mt-5">
Подкатегории с раскрытием как в основном списке ....
</div>

{{ \App\Forms\ModalDelete::create('Вы уверены?',
    'Вы действительно хотите удалить категорию?<br>Этот процесс не может быть отменен.')->show() }}
@endsection
