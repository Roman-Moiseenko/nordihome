@extends('layouts.side-menu')

@section('subcontent')

    <h1 class="text-lg mb-5">Режим тестирования</h1>
    <div class="box p-3">
        <h2 class="text-base font-medium mb-2">Последние изменения:</h2>
        @foreach(App\Modules\Base\Helpers\Version::updated() as $date => $actions)
            <h3 class="font-medium mt-3 mb-1">{{ $date }}</h3>
            <ul>
                @foreach($actions as $action)
                    <li><span class="circle green"></span>{{ $action }}</li>
                @endforeach
            </ul>
        @endforeach
    </div>



@endsection
