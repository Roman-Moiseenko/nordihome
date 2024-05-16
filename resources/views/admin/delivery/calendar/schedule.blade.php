@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            График доставки по области
        </h2>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-5">
        <!-- Управление -->

    </div>
    <div class="grid grid-cols-7 gap-6 mt-5 w-100">
    @for($i = 0; $i < 7; $i++)
        <div class="col-span-6 md:col-span-3 lg:col-span-1 text-center items-center">
            <livewire:admin.delivery.calendar.schedule-week :week="$i" :days="$days" />
        </div>
    @endfor
    </div>
        @foreach($days as $month_name => $month_days)
            <h2 class="font-medium text-lg text-center mt-3 mb-1">{{ $month_name }}</h2>
            <div class="grid grid-cols-7 gap-6 mt-5 w-100">
                @for($i = 1; $i < $month_days[0]['week']; $i++)
                    <div class="col-span-6 md:col-span-3 lg:col-span-1">
                    </div>
                @endfor
                @foreach($month_days as $day)
                    <div class="col-span-6 md:col-span-3 lg:col-span-1">
                        <livewire:admin.delivery.calendar.schedule-day :year="$day['year']" :month="$day['month']" :day="$day['day']" :disabled="$day['disabled']" />
                    </div>
                @endforeach
            </div>
        @endforeach


@endsection

