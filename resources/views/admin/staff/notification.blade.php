@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-5">
        <h1 class="text-lg font-medium mr-auto">
            Уведомления
        </h1>
    </div>
    <div class="grid grid-cols-12 gap-4 mt-5">
        <!-- Управление -->
        <div class="col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            {{ $notifications->links('admin.components.count-paginator') }}
        </div>
        <div class="box col-span-12 overflow-auto lg:overflow-visible p-4">
            <x-base.table class="table table-hover">
                <x-base.table.thead class="table-dark">
                    <x-base.table.tr>
                        <x-base.table.th class="whitespace-nowrap"></x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ЗАГОЛОВОК</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">СООБЩЕНИЕ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДАТА СОЗДАНИЯ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ДАТА ПРОЧТЕНИЯ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ПЕРЕЙТИ</x-base.table.th>
                        <x-base.table.th class="text-center whitespace-nowrap">ОТМЕТИТЬ</x-base.table.th>
                    </x-base.table.tr>
                </x-base.table.thead>
                <x-base.table.tbody>
                    <input id="route" type="hidden"  />
                    @foreach($notifications as $notification)
                        <x-base.table.tr>
                            <x-base.table.td class="{{ is_null($notification->read_at) ? 'text-warning' : '' }}">
                                @if(empty($notification->data['icon']))
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-ring"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><path d="M4 2C2.8 3.7 2 5.7 2 8"/><path d="M22 8c0-2.3-.8-4.3-2-6"/></svg>
                                @else
                                    <x-base.lucide icon="{{ $notification->data['icon'] }}" />
                                @endif
                            </x-base.table.td>
                            <x-base.table.td class="text-center">{{ $notification->data['title'] }}</x-base.table.td>
                            <x-base.table.td class="text-center">{{ $notification->data['message'] }}</x-base.table.td>
                            <x-base.table.td class="text-center">{{ $notification->created_at->translatedFormat('j F Y H:i:s') }}</x-base.table.td>
                            <x-base.table.td class="text-center">
                                {{ (is_null($notification->read_at)) ? '-' : $notification->read_at->translatedFormat('j F Y H:i:s') }}
                            </x-base.table.td>
                            <x-base.table.td class="text-center">
                                @if(!empty($notification->data['route']))
                                    <a href="{{ $notification->data['route'] }}" class="text-success font-medium link-read"
                                       data-id="{{ $notification->id }}" data-route="{{ route('admin.staff.notification-read', $notification) }}">
                                        Перейти
                                    </a>
                                @endif
                            </x-base.table.td>

                            <x-base.table.td class="table-report__action w-56">
                                @if(is_null($notification->read_at))
                                <button class="btn btn-warning-soft button-read"
                                        data-id="{{ $notification->id }}"
                                        data-route="{{ route('admin.staff.notification-read', $notification) }}"
                                        type="button"
                                        >Прочитано</button>
                                @endif
                            </x-base.table.td>
                        </x-base.table.tr>
                    @endforeach
                </x-base.table.tbody>
            </x-base.table>
        </div>
    </div>
    {{ $notifications->links('admin.components.paginator', ['pagination' => $pagination]) }}


    <script>

        let buttonsRead = document.querySelectorAll('.button-read');
        let linksRead = document.querySelectorAll('.link-read');
        Array.prototype.forEach.call(buttonsRead, function (_button) {
            _button.addEventListener('click', function () {
                setAjax(_button.dataset.route, true);
            });
        });

        Array.prototype.forEach.call(linksRead, function (_link) {
            _link.addEventListener('click', function () {
                setAjax(_link.dataset.route, false);
            });
        });
        function setAjax(route, reboot) {
            let _params = '_token=' + '{{ csrf_token() }}';
            let request = new XMLHttpRequest();
            request.open('POST', route);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(_params);
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    let data = JSON.parse(request.responseText);

                    if (data.error !== undefined) {
                        window.notification('Ошибка',data.error ,'danger');
                        return;
                    }
                    if (reboot === true) {
                        window.location.reload();
                    }
                }
            };
        }
    </script>

    <div class="mt-3">
        <livewire:admin.notification-table />
    </div>
@endsection
