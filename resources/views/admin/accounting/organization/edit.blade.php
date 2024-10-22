@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование организации
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.accounting.organization.update', $organization) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-company.fields :company="$organization" />

        <button type="submit" class="btn btn-success mt-5">Сохранить</button>
    </form>
@endsection
