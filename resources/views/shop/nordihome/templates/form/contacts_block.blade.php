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
<div class="m-t_30">
    <div id="{{ $widget->id }}" class="feedback" not-hide>
        <div class="m-b_10 m-t_10"><label class="width_100"> {{ $widget->fields["name"] }} <input name="name" class="width_100" required placeholder="Елена"/></div>
        <div>
            <input name="email" class="form-control" required placeholder="{{ $widget->fields["email"] }}"/>
        </div>
        <div>
            <input name="name" required placeholder="{{ $widget->fields["name"] }}"/>
        </div>
        <div>
            <input name="wish" class="form-control" placeholder="Пожелания"/>
        </div>
        <hr/>
        <textarea name="message" class="form-control" placeholder="Сообщение"></textarea>
        <button class="btn btn-success" type="button">Ответить</button>
    </div>
    <div id="{{ $widget->id }}-callback" style="display: none">
        *** Спасибо за ваше обращение ***
    </div>


</div>




