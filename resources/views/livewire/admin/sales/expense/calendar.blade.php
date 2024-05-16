<div>
    <h2 class="text-xl font-medium mb-2">Календарь отгрузок</h2>
    <div class="grid grid-cols-12 gap-x-6">
    @foreach($days as $day)
        <div class="col-span-2 rounded-md border border-slate-200/60 p-1 mt-3 {{ $day->date_at->isWeekend() ? 'text-danger' : '' }}">
            <h2 class="text-center text-lg">{{ $day->htmlDate() }}</h2>
            <h3 class="text-center text-base mb-1">{{ $day->date_at->translatedFormat('l') }}</h3>
            @foreach($day->periods as $period)
                    <div class="form-check">
                        <input id="period-{{ $period->id }}" class="form-check-input" name="period" type="radio" value="{{ $period->id }}"
                               wire:change="set_period" wire:model="period" wire:loading.attr="disabled"
                        >
                        <label class="form-check-label {{ $period->isFull() ? 'text-danger' : ''}}" for="period-{{ $period->id }}">
                            {{ $period->timeHtml() }} ({{ $period->freeWeight() }} кг, {{ $period->freeVolume() }} м3)
                        </label>
                    </div>
            @endforeach
        </div>
    @endforeach
    </div>
</div>
