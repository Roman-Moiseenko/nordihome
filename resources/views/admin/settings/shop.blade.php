@extends('layouts.side-menu')

@section('subcontent')
    <form method="POST" action="{{ route('admin.settings.shop') }}">
        @csrf

        <div class="box">
            <ul class="nav nav-boxed-tabs" role="tablist">
                @foreach($groups as $key => $items)
                    <li id="tab-{{ $key }}" class="nav-item w-52" role="presentation">
                        <button class="nav-link w-full py-2 {{ ($key == 'common') ? 'active' : '' }}"
                                data-tw-toggle="pill"
                                data-tw-target="#{{ $key }}-tab" type="button" role="tab"
                                aria-controls="{{ $key }}-tab" aria-selected="{{ ($key == 'common') ? 'true' : 'false' }}">{{ ucfirst($key) }}</button> </li>
                @endforeach
            </ul>
            <div class="tab-content mt-5">
                @foreach($groups as $key => $items)
                <div id="{{ $key }}-tab"
                     class="tab-pane leading-relaxed {{ ($key == 'common') ? 'active' : '' }}" role="tabpanel" aria-labelledby="tab-{{ $key }}">
                    @foreach($items as $item)
                        @include('admin.settings._list', ['item' => $item])
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Сохранить настройки</button>
        </div>
    </form>

    <script>
        let tabButtons = document.querySelectorAll('.nav-link[role=tab]');
        let tabPanels = document.querySelectorAll('.tab-pane[role=tabpanel]');
        tabButtons.forEach(function (_button) {
            _button.addEventListener('click', function () {
                tabButtons.forEach(function (item) {
                    item.classList.remove('active');
                });
                tabPanels.forEach(function (_panel) {
                    _panel.classList.remove('active');
                });
                _button.classList.add('active');
                document.getElementById(_button.getAttribute('aria-controls')).classList.add('active');
            });
        });
    </script>
@endsection
