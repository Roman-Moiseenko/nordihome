@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Добавление хранилища
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.storage.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="parent_id" autocomplete="off">
        @include('admin.accounting.storage._fields-form', ['storage' => null])
    </form>

@endsection
