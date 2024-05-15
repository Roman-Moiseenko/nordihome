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
            <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                <form method="get" action="{{ url()->current() }}">
                    <input type="radio" class="btn-check" name="filter" id="option2" autocomplete="off"
                           value="new" onclick="this.form.submit();" @if($filter == 'new') checked @endif>
                    <label class="btn btn-success" for="option2">Новые</label>


                    <input type="radio" class="btn-check" name="filter" id="option6" autocomplete="off"
                           value="completed" onclick="this.form.submit();" @if($filter == 'completed') checked @endif>
                    <label class="btn btn-secondary" for="option6">Завершенные</label>

                    <input type="radio" class="btn-check" name="filter" id="option1" autocomplete="off"
                           value="all" onclick="this.form.submit();" @if($filter == 'all') checked @endif>
                    <label class="btn btn-primary" for="option1">Все</label>

                </form>
            </div>

            {{ $calendars->links('admin.components.count-paginator') }}

        </div>

    </div>
    <div class="grid grid-cols-12 gap-6 mt-5 w-100">
        @foreach($calendars as $calendar)
            <div class="intro-y col-span-6 md:col-span-4 lg:col-span-3">
                @include('admin.delivery.calendar._calendar', ['calendar' => $calendar])
            </div>
        @endforeach
    </div>
    {{ $calendars->links('admin.components.paginator', ['pagination' => $pagination]) }}
@endsection

