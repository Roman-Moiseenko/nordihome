<div>
    <div class="box p-2 m-1 {{ $date->isWeekend() ? 'text-danger' : '' }}">
        <h2 class="text-center font-bold text-base">{{ $date->format('d') }}</h2>
        @foreach($periods as $key => $period)
            <div class="form-check ml-2">
                <input id="time-{{ $date->format('d-m-y') . '-' . $key }}" class="form-check-input" type="checkbox" value="{{ $key }}"
                       wire:change="save" wire:model="periods.{{ $key }}.checked" wire:loading.attr="disabled"
                       @if($disabled) disabled @endif
                >
                <label class="form-check-label" for="time-{{ $date->format('d-m-y') . '-' . $key }}">{{ $period['name'] }}</label>
            </div>
        @endforeach
    </div>
</div>
