<div>
    <span class="font-bold text-lg">{{ $WEEKS[$week] }}</span>
    <div class="form-check">
        <input id="week-{{ $week }}" class="form-check-input  mx-auto" type="checkbox" value="{{ $week }}"
               wire:change="save" wire:model="checked" wire:loading.attr="disabled"
        >
    </div>
</div>
