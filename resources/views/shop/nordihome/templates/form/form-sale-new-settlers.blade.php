<!--template: Форма - акция для новоселов-->
@php
    /**
    * $widget->name
    * $widget->url
    * $widget->caption
    * $widget->description
    * $widget->fields
    * $widget->lists
    */

    /*
    * <div id="{{ $widget->id }}" class="feedback" not-hide> -
        * Основной блок, где находятся поля данных и кнопка отправки сообщения
        * атрибут not-hide - не скрывать после отправки
        * <div id="{{ $widget->id }}-callback" style="display: none"> -
            * блок, который показывается, после отправки сообщения
            */
            use App\Modules\Page\Entity\FormWidget;
            /** @var FormWidget $widget  */
@endphp
<div class="parser-fos p-t_50 p-b_50">
    <div class="container">
        <div class="t-t_uppercase f-z_35 t-a_center f-w_600">Остались вопросы?</div>
        <div class="t-a_center m-t_10 m-b_20">Мы готовы ответить на Ваши вопросы: заполните форму ниже, и наш менеджер перезвонит Вам в ближайшее время.</div>
        <div id="{{ $widget->id }}" class="feedback" not-hide>
            <div class="row">
                <div class="col-md-6 col-lg-2">
                    <label>
                        <input name="name" type="text" required placeholder="{{ $widget->fields["name"] }}"/>
                    </label>
                </div>
                <div class="col-md-6 col-lg-3">
                    <label>
                        <input name="phone" type="tel" required placeholder="{{ $widget->fields["phone"] }}"/>
                    </label>
                </div>
                <div class="col-md-9 col-lg-5">
                    <label>
                        <textarea placeholder="{{ $widget->fields["question"] }}"></textarea>
                    </label>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label><button class="btn-form" type="button">ОТПРАВИТЬ</button></label>
                </div>
                <div class="col-12">
                    <label class="f-z_14">
                        <input type="checkbox" name="agreement" value="{{ $widget->fields["agreement"] }}"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
                    </label>
                </div>
            </div>
        </div>
        <div id="{{ $widget->id }}-callback" class="form-send-message" style="display: none">
            Спасибо за Ваше сообщение. Оно успешно отправлено. Наш менеджер свяжется с Вами в ближайшее время.
        </div>
    </div>
</div>
