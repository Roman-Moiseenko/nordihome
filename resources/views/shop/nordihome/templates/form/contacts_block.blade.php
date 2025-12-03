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
use App\Modules\Page\Entity\FormWidget;
       /** @var FormWidget $widget  */
@endphp

    <div class="heading f-w_600 f-z_23 m-b_20">ФОРМА ОБРАТНОЙ СВЯЗИ</div>
    <div>По вопросам сотрудничества: <a href="mailto:partnership@nordihome.ru">partnership@nordihome.ru</a></div>
    <div class="m-t_30">
        <div id="{{ $widget->id }}" class="feedback" not-hide>
            <h2 class="fw-semibold mt-5">{{ $widget->caption }}</h2>
            <h3>{{ $widget->description }}</h3>
            <div>
                <input name="email" class="form-control" required placeholder="{{ $widget->fields["email"] }}"/>
            </div>
            <div>
                <input name="user" class="form-control" required placeholder="{{ $widget->fields["name"] }}"/>
            </div>
            <div>
                <select name="ask" class="form-control">
                    @foreach($widget->lists->get('ask') as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
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




