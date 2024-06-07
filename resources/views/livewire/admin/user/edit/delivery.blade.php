<div>
    <div class="flex items-center">
    <x-base.lucide icon="map" class="w-4 h-4"/>&nbsp;{{ $user->htmlDelivery() }}&nbsp;
    @if($edit)
        <button class="btn btn-warning-soft btn-sm ml-1">
            <x-base.lucide icon="pencil" class="w-4 h-4"/>
        </button>
    @endif
    </div>
</div>
