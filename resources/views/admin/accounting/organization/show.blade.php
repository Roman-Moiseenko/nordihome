@extends('layouts.side-menu')

@section('subcontent')
    <div>
        <div class="intro-y flex items-center mt-8">
            <h1 class="text-lg font-medium mr-auto">
                {{ $organization->name }}
            </h1>
        </div>
    </div>
    <div class="font-medium text-xl text-danger mt-6">
        В разработке. Сведения об организации
        <ol>
            <li>???: </li>

        </ol>

    </div>
@endsection
