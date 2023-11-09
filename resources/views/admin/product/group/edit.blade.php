@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактировать группу {{ $group->name }}
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.group.update', $group) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-12 gap-6 mt-5">
            <!-- Основные данные -->
            <div class="intro-y col-span-12">
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
                                {{ \App\Forms\Input::create('name', ['placeholder' => 'Название группы', 'value' => $group->name])->show() }}
                                {{ \App\Forms\TextArea::create('description', ['placeholder' => 'Описание', 'value' => $group->description, 'class' => 'mt-3'])->rows(9)->show() }}
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                <div class="w-52">
                                {{ \App\Forms\Upload::create('file', $group->photo->getUploadUrl())->show() }}
                                </div>
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
