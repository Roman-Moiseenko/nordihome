<!--template:Задать вопрос - блок контакты-->
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
use App\Modules\Content\Entity\Widgets\FormWidget;
       /** @var FormWidget $widget  */
@endphp

<div class="heading f-w_600 f-z_23 m-b_20">ФОРМА ОБРАТНОЙ СВЯЗИ</div>
<div>По вопросам сотрудничества:
    @if(isset($contacts['mail_1']))
        <a href="{{ $contacts['mail_1']->url }}">{{ $contacts['mail_1']->name }}</a>
    @endif
</div>
<div>
    <div id="{{ $widget->id }}" class="feedback" not-hide>
        <div class="m-b_10 m-t_10">
            <label> {{ $widget->fields["name"] }} <input name="name" class="width_100" required placeholder="Елена"/></label>
        </div>
        <div class="m-b_10">
            <label> {{ $widget->fields["phone"] }} <input name="phone" class="width_100" required placeholder="+79097589135"/></label>
        </div>
        <div class="m-b_10">
            <label>{{ $widget->fields["question"] }} <textarea class="width_100" placeholder="Мой вопрос"></textarea>
            </label>
        </div>
        <div class="m-b_10">
            <label>Ваш ник в Telegram <input name="telegram" class="width_100" required placeholder="Пример: @username"/></label>
        </div>
        <div class="m-b_10">
            <label>Выберите удобный способ для связи с Вами <select class="width_100"><option value="">—Выберите вариант—</option><option value="Позвонить по телефону">Позвонить по телефону</option><option value="Написать на почту">Написать на почту</option><option value="Написать в Телеграм">Написать в Телеграм</option><option value="Написать в Макс">Написать в Макс</option></select></label>
        </div>
        <div>
            <label class="f-z_14">
                <input type="checkbox" name="agreement" value="{{ $widget->fields["agreement"] }}"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
            </label>
        </div>
        <div>
            <label><button class="btn-form width_100" type="button">Отправить</button></label>
        </div>
        <input type="hidden" name="form" value="Форма с блока контактов">
    </div>
    <div id="{{ $widget->id }}-callback" style="display: none">
        Спасибо за Ваше сообщение. Оно успешно отправлено. Наш менеджер свяжется с Вами в ближайшее время.
    </div>


</div>




