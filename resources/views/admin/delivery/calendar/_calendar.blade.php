<div class="box p-3">
    <h2 class="text-lg font-medium text-center">{{ $calendar->htmlDate() }}</h2>
    @foreach($calendar->periods as $period)
        <div class="text-base font-medium mb-2 mt-3">
            {{ $period->timeHtml() }}
        </div>
        @foreach($period->expenses as $expense)
            <div class="mt-1">
                <a href="{{ route('admin.sales.expense.show', $expense) }}" class="text-success">
                    {{ $expense->address->address }} ({{ $expense->getWeight() . ' кг ' . $expense->getVolume() . ' м3'}})
                </a>
            </div>
        @endforeach
    @endforeach
</div>
