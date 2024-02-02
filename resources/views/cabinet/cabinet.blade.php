@extends('layouts.shop')

@section('body')
    cabinet
@endsection

@section('main')
    container-xl cabinet
@endsection

@section('content')
    <div class="title-page">
        <h1>@yield('h1')</h1>
    </div>
    <div class="d-flex">
        <div class="left-list-block">
            @yield('subcontent')
        </div>
        <div class="right-action-block">
            <div class="sticky-block">
                <div class="cabinet-menu-block">
                    <ul class="cabinet-menu">
                        @foreach(App\Modules\Shop\MenuHelper::getCabinetMenu() as $item)
                            <li class="cabinet-menu-item {{ ($item['url'] == request()->url()) ? 'active' : '' }}">
                                <a href="{{ $item['url'] }}">{{ $item['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
