@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Календарь доставки по области
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">


            {{ $calendars->links('admin.components.count-paginator') }}

        </div>
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
        </div>
    </div>

    {{ $calendars->links('admin.components.paginator', ['pagination' => $pagination]) }}
@endsection

