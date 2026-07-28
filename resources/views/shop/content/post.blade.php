@php
    use App\Modules\Shop\Application\DTOs\Pages\PostViewPageData;
    /** @var PostViewPageData  $pageData */
@endphp
@extends('shop.layouts.main')
@section('title', $pageData->meta->title)
@section('description', $pageData->meta->description)
@section('body', 'post')

@section('main', 'container-xl')
@section('content')
    <h1>{{ $pageData->post->caption }}</h1>
    <!-- Виджет содержания -->
    @include('shop.content._table_contents', ['items' => $pageData->tableContents])
    @foreach($pageData->blocks as $block)
        @if($block->section == "content")
            <div class="widget mt-4">
                @include('widgets::' . $block->widget->category . '.' . $block->widget->slug,
                [
                    'params' => $block->widget->params,
                    'widget' => $block->widget->id,
                ])
            </div>
        @endif
    @endforeach

    <script type="application/ld+json" class="schemantra.com">
        {!! json_encode($pageData->schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('bottom-content')
    @foreach($pageData->blocks as $block)
        @if($block->section == "bottom-content")
            <div class="widget mt-4">
                @include('widgets::' . $block->widget->category . '.' . $block->widget->slug,
                [
                    'params' => $block->widget->params,
                    'widget' => $block->widget->id,
                ])
            </div>
        @endif
    @endforeach


    <div class="parser-fos p-t_50 p-b_50 m-t_20">
        <div class="container">
            <div class="t-t_uppercase f-z_35 t-a_center f-w_600">Остались вопросы?</div>
            <div class="t-a_center m-t_10 m-b_20">Мы готовы ответить на Ваши вопросы: заполните форму ниже, и наш менеджер перезвонит Вам в ближайшее время.</div>
            <div id="post" class="feedback-form" not-hide="">
                <input type="hidden" name="form" value="Записи блога. Остались вопросы"/>
                <div class="row">
                    <div class="col-md-6 col-lg-2">
                        <label>
                            <input name="name" type="text" required="" placeholder="Имя и Фамилия">
                        </label>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label>
                            <input name="phone" type="tel" required="" placeholder="Ваш телефон: +79097589135">
                        </label>
                    </div>
                    <div class="col-md-9 col-lg-5">
                        <label>
                            <textarea placeholder="Опишите Ваш вопрос или оставьте это поле пустым"></textarea>
                        </label>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <label><button class="btn-form" type="button">ОТПРАВИТЬ</button></label>
                    </div>
                    <div class="col-12">
                        <label class="f-z_14">
                            <input type="checkbox" name="agreement" value="Согласие на обработку персональных данных"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
                        </label>
                    </div>
                </div>
            </div>
            <div id="post-callback" class="form-send-message" style="display: none">
                Спасибо за Ваше сообщение. Оно успешно отправлено. Наш менеджер свяжется с Вами в ближайшее время.
            </div>
        </div>
    </div>
@endsection
