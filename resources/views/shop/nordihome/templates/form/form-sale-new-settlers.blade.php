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
<div>
    <div id="{{ $widget->id }}" class="feedback" not-hide>
        <div>
            <label>{{ $widget->fields["name"] }}<br>
                <input name="name" type="text" required placeholder="Елена"/>
            </label>
        </div>
        <div>
            <label>
                <input name="phone" type="tel" required placeholder="{{ $widget->fields["phone"] }}"/>
            </label>
        </div>
        <div>
            <label class="f-z_14">
                <input type="checkbox" name="agreement" value="{{ $widget->fields["agreement"] }}"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
            </label>
        </div>
        <div>
            <label><button class="btn-form" type="button">ОТПРАВИТЬ</button></label>
        </div>
    </div>
    <div id="{{ $widget->id }}-callback" class="form-send-message" style="display: none">
        Спасибо за Ваше сообщение. Оно успешно отправлено. Наш менеджер свяжется с Вами в ближайшее время.
    </div>
</div>
