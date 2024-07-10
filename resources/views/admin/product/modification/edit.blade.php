@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактировать модификацию {{ $modification->name }}
        </h2>
    </div>

@endsection
