@extends('layouts.side-menu')

@section('subcontent')
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Редактирование бренда
        </h2>
    </div>
    <form method="POST" action="{{ route('admin.product.brand.update', $brand) }}" enctype="multipart/form-data">
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
                                {{ \App\Forms\Input::create('name', ['placeholder' => 'Бренд', 'value' => $brand->name])->show() }}
                                {{ \App\Forms\Input::create('url', ['placeholder' => 'Ссылка на официальный сайт', 'value' => $brand->url, 'class' => 'mt-3'])->show() }}

                                {{ \App\Forms\TomSelect::create('sameAs[]', ['placeholder' => 'Введите ссылку на упоминания бренда', 'class' => 'mt-3'])
                                    ->header('Поле SameAs')->options($brand->getSameAs())->selected($brand->getSameAs())->show() }}
                            </div>
                            <div class="col-span-12 lg:col-span-4">
                                {{ \App\Forms\TextArea::create('description', ['placeholder' => 'Описание', 'value' => $brand->description])->rows(9)->show() }}
                            </div>
                            <div id="single-file-upload" class="col-span-12 lg:col-span-4">
                                {{ \App\Forms\Upload::create('file', $brand->photo->getUploadUrl())->show() }}
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
