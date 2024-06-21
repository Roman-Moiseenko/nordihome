@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Добавление новой организации
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.organization.store') }}" enctype="multipart/form-data">
        @csrf

        @include('admin.accounting.organization._fields-form', ['organization' => null])
        <button type="submit" class="btn btn-success mt-5">Сохранить</button>
    </form>

@endsection


