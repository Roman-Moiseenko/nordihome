@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Добавить новый контакт
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.page.contact.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.page.contact._fields-form', ['contact' => null])
    </form>

@endsection
