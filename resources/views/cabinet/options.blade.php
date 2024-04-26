@extends('cabinet.cabinet')


@section('title', 'Мой кабинет - NORDI HOME')

@section('h1', 'Настройки')

@section('subcontent')

    <h3 class="mt-3">Подписки на Уведомления</h3>
    <div class="box-card view-option">
        @foreach($subscriptions as $subscription)
            <div>
                <x-widget.check id="subscription-{{ $subscription->id }}" name="subscription" class="mt-3 subscription-check"
                                route="{{ route('cabinet.options.subscription', $subscription) }}"
                                checked="{{ $user->isSubscription($subscription) }}" >
                    {{ $subscription->title }}
                </x-widget.check>
            </div>
        @endforeach
    </div>





    <h3 class="mt-3">Рассылки</h3>

@endsection

