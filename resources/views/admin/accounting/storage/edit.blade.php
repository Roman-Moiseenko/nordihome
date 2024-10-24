@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование хранилища
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.storage.update', $storage) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.accounting.storage._fields-form', ['storage' => $storage])
    </form>
@endsection
