<div class="box w-full mt-3 overflow-hidden">
    <div class="flex flex-row">
        <div class="w-32 grid text-lg font-medium text-center items-center border-r bg-primary text-white bg-slate-600">
            {{ $calendar->date_at->format('d') }}<br>{{ $calendar->date_at->translatedFormat('F') }}
        </div>
        <div class="">
        @foreach($calendar->periods as $period)
            <div class="border-b flex flex-row w-full">
                <div class="w-32 grid items-center text-center text-base font-medium mb-2 mt-3">
                    {{ $period->timeHtml() }}
                </div>
                <div class="ml-2 ">
                    @foreach($period->expenses as $i => $expense)
                        <div class="mt-1 flex">
                            <div class="w-10 font-medium">{{ $i + 1 }}.</div>
                            <div class="w-56">
                                {{ $expense->address->address }}
                            </div>
                            <div class="w-40 text-center">
                                {{ $expense->getWeight() . ' кг ' . $expense->getVolume() . ' м3'}}
                            </div>
                            <div class="w-56 text-right">
                                <a href="{{ route('admin.order.expense.show', $expense) }}" class="text-success">
                                {{ $expense->htmlNum() . ' - ' . $expense->statusHtml() }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>
