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
use App\Modules\Page\Entity\Widgets\FormWidget;
       /** @var FormWidget $widget  */
@endphp

<div class="heading f-w_600 f-z_23 m-b_20">ФОРМА ОБРАТНОЙ СВЯЗИ</div>
<div>По вопросам сотрудничества: <a href="mailto:partnership@nordihome.ru">partnership@nordihome.ru</a></div>
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
        <div>
            <label class="f-z_14">
                <input type="checkbox" name="agreement" value="{{ $widget->fields["agreement"] }}"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
            </label>
        </div>
        <div>
            <label><button class="btn-form" type="button" class="width_100" >ОТПРАВИТЬ</button></label>
        </div>
    </div>
    <div id="{{ $widget->id }}-callback" style="display: none">
        Спасибо за Ваше сообщение. Оно успешно отправлено. Наш менеджер свяжется с Вами в ближайшее время.
    </div>


</div>




