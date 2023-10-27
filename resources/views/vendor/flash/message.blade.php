@foreach (session('flash_notification', collect())->toArray() as $message)
    @if ($message['overlay'])
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title'      => $message['title'],
            'body'       => $message['message']
        ])
    @else
        <x-base.alert class="mb-2 flex items-center" variant="{{$message['level']}}">
            <x-base.lucide class="mr-2 h-6 w-6" icon="AlertCircle"/>
            {!! $message['message'] !!}
            <x-base.alert.dismiss-button class="text-white" type="button" aria-label="Close">
                <x-base.lucide class="h-4 w-4" icon="X"/>
            </x-base.alert.dismiss-button>
        </x-base.alert>
    @endif
@endforeach

{{ session()->forget('flash_notification') }}
