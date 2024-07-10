@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактировать подписку
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.user.subscription.update', $subscription) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf

        <div class="grid grid-cols-12 gap-6 mt-5">
            <!-- Основные данные -->
            <div class="col-span-12">
                <div class="box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Внесите данные
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-12 lg:col-span-4">

                                {{ \App\Forms\Input::create('name',
                                ['value' => $subscription->name])
                                ->label('Имя')->show() }}

                                {{ \App\Forms\Input::create('title',
                                ['class' => 'mt-3', 'value' => $subscription->title])
                                ->label('Заголовок для клиентов')->show() }}

                                {{ \App\Forms\Input::create('description',
                                ['class' => 'mt-3', 'value' => $subscription->description])
                                ->label('Описание для клиентов')->show() }}

                            </div>
                            <div class="col-span-12 lg:col-span-4">

                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 lg:col-span-4 p-5">
                        <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
