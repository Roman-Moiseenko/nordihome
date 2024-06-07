<div>
    @if(!is_null($notification))
    <div class="cursor-pointer relative flex items-center mt-5">
        <div class="mr-auto text-warning w-10">
            @if(empty($notification->data['icon']))
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-ring"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/><path d="M4 2C2.8 3.7 2 5.7 2 8"/><path d="M22 8c0-2.3-.8-4.3-2-6"/></svg>
            @else
                <x-base.lucide icon="{{ $notification->data['icon'] }}" />
            @endif
        </div>
        <div class="ml-2 overflow-hidden w-full" wire:click="follow">
            <div class="flex items-center">
                <a class="mr-5 truncate font-medium"
                    href="">
                    {{ $notification->data['title'] }}
                </a>
                <div class="ml-auto whitespace-nowrap text-xs text-slate-400">
                    {{ $notification->created_at->format('H:i:s') }}
                </div>
            </div>
            <div class="mt-0.5 w-full truncate text-slate-500">
                {{ $notification->data['message'] }}
            </div>
        </div>
        <div class="text-danger items-center text-center font-medium text-lg m-auto w-10 flex-none" wire:click="remove">
            X
        </div>
    </div>
    @endif
</div>

<script>

    document.addEventListener('livewire:init', () => {
        Livewire.hook('morph.added',  ({ el }) => {
            lucide.createIcons();
        });
        Livewire.on('lucide-icons', (event) => {
            lucide.createIcons();
        });
    });
</script>


