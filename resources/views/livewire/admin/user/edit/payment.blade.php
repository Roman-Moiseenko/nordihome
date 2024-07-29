<div>
    {{-- Be like water. --}}

    <div class="flex items-center" @if($change) style="display: none" @endif>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="coins"
             class="lucide lucide-coins stroke-1.5 w-4 h-4">
            <circle cx="8" cy="8" r="6"></circle>
            <path d="M18.09 10.37A6 6 0 1 1 10.34 18"></path>
            <path d="M7 6h1v4"></path>
            <path d="m16.71 13.88.7.71-2.82 2.82"></path>
        </svg>
        <span class="ml-2">{{ $html_payment }}</span>
        @if($edit)
            <button class="btn btn-warning-soft btn-sm ml-1" wire:click="open_change" disabled title="Изменение не доступно">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     data-lucide="pencil" class="lucide lucide-pencil stroke-1.5 w-4 h-4">
                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                    <path d="m15 5 4 4"></path>
                </svg>
            </button>
        @endif
    </div>
</div>
