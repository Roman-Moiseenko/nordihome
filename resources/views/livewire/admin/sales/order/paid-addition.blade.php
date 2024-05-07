<div>
    <div class="box-in-box flex items-center p-2">
        <div class="w-20 text-center">{{ $i + 1 }}</div>
        <div class="w-56 text-center">{{ $addition->purposeHTML() }}</div>
        <div class="w-40 input-group">
            <input id="addition-amount-{{ $addition->id }}" type="number" class="form-control text-right update-data-ajax"
                   value="{{ $addition->getRemains() }}" aria-describedby="addition-amount"
                   min="0" max="{{ $addition->getRemains() }}" {{ $addition->getRemains() == 0 ? 'disabled' : '' }}
                   wire:change="set_amount" wire:model="amount" wire:loading.attr="disabled"
            >
            <div id="addition-amount" class="input-group-text">₽</div>
        </div>

        <div class="w-56 text-center">{{ $addition->comment }}</div>
        <div class="w-20 text-center">
            <div class="form-check form-switch justify-center mt-3">
                <input id="addition-check-{{ $addition->id }}" class="form-check-input update-data-ajax" type="checkbox"
                    {{ $addition->getRemains() == 0 ? 'disabled' : 'checked' }}
                    data-input="addition-amount-{{ $addition->id }}" name="additions"
                       wire:change="toggle_enabled" wire:model="enabled" wire:loading.attr="disabled"
                       value="{{ $addition->id }}"
                >
                <label class="form-check-label" for="additions-check-{{ $addition->id }}"></label>
            </div>
        </div>
    </div>

</div>
