<!--template:Страница кухни под заказ - форма наверху-->
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
<div class="main-kitchen">
    <img src="/uploads/gallery/1/main-kuhni-min.jpg" class="width_100" alt="Кухни икеа под заказ">
    <div class="main-kitchen-form bg-white">
        <div class="t-t_uppercase f-w_600 t-a_center f-z_23 m-b_10">Получите <span class="t-color_orange">дизайн-проект в подарок!*</span></div>
        <div class="t-a_center m-b_10">Наш дизайнер с опытом работы более 10 лет в ИКЕА разработает дизайн-проект вашей кухни!</div>
        <div class="m-b_10">
            <div id="{{ $widget->id }}" class="feedback" not-hide>
                <div class="row">
                    <div class="col-md-6 col-lg-6 m-b_5">
                        <label>
                            <input name="name" required placeholder="{{ $widget->fields["name"] }}"/>
                        </label>
                    </div>
                    <div class="col-md-6 col-lg-6 m-b_5">
                        <label>
                            <input name="phone" required placeholder="{{ $widget->fields["phone"] }}"/>
                        </label>
                    </div>
                    <div class="col-md-6 col-lg-6 m-b_5">
                        <label>
                            <input name="city" required placeholder="{{ $widget->fields["city"] }}"/>
                        </label>
                    </div>
                    <div class="col-md-6 col-lg-6 m-b_5">
                        <label><button class="btn-form" type="button">ОСТАВИТЬ ЗАЯВКУ</button></label>
                    </div>
                    <div class="col-12">
                        <label class="f-z_14">
                            <input type="checkbox" name="agreement" value="{{ $widget->fields["agreement"] }}"> Я <a href="/soglasie-na-obrabotku-personalnyh-dannyh/" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/politika-obrabotki-personalnyh-dannyh/" target="_blank">политике конфиденциальности</a>
                        </label>
                    </div>
                </div>
            </div>
            <div id="{{ $widget->id }}-callback" class="form-send-message" style="display: none">
                *** Спасибо за ваше обращение ***
            </div>
        </div>
        <div class="f-z_12">*Услуга по разработке дизайн-проекта предоставляется бесплатно при 100% оплате кухни под заказ (базовая стоимость составляет 4 000 рублей). Подробности у менеджеров.</div>
    </div>
</div>




<div class="heading f-w_600 f-z_23 m-b_20">ФОРМА ОБРАТНОЙ СВЯЗИ</div>
<div>По вопросам сотрудничества: <a href="mailto:partnership@nordihome.ru">partnership@nordihome.ru</a></div>
<div class="m-t_30">
    <div id="{{ $widget->id }}" class="feedback" not-hide>
        <h2 class="fw-semibold mt-5">{{ $widget->caption }}</h2>
        <h3>{{ $widget->description }}</h3>

        <div>
            <input name="user" class="form-control" required placeholder="{{ $widget->fields["name"] }}"/>
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


