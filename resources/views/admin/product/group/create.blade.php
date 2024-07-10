@extends('layouts.side-menu')

@section('subcontent')
    <div class="flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Создать группу
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.group.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-12 gap-6 mt-5">
            <!-- Основные данные -->
            <div class="col-span-12">
                <div class="intro-y box">
                    <div
                        class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">
                            Внесите данные
                        </h2>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-12 lg:col-span-4">
                                {{ \App\Forms\Input::create('name', ['placeholder' => 'Название группы'])->show() }}
                                {{ \App\Forms\TextArea::create('description', ['placeholder' => 'Описание', 'class' => 'mt-3'])->rows(9)->show() }}
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                {{ \App\Forms\Upload::create('file')->show() }}
                            </div>

                        </div>
                    </div>
                    <div class="intro-y col-span-12 lg:col-span-4 p-5">
                        <button type="submit" class="btn btn-primary shadow-md mr-2 ml-auto">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
