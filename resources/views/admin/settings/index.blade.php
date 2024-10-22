@extends('layouts.side-menu')

@section('subcontent')

    <div class="grid grid-cols-12 gap-4 mt-5">
        @foreach($settings as $setting)
            <div class="col-span-12 md:col-span-6 lg:col-span-4 box p-4">
                <a href="{{ route('admin.setting.' . $setting->slug) }}" class="text-base font-medium text-primary mt-3">
                    {{ $setting->name }}
                </a>

                <div class="mt-4">
                    {{ $setting->description }}
                </div>
            </div>
        @endforeach

    </div>

@endsection
