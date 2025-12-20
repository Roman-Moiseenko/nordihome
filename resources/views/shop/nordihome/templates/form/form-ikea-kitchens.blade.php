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
                            <input type="checkbox" name="agreement" value="{{ $widget->fields["agreement"] }}"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
                        </label>
                    </div>
                </div>
            </div>
            <div id="{{ $widget->id }}-callback" class="form-send-message" style="display: none">
                Спасибо за Ваше сообщение. Оно успешно отправлено. Наш менеджер свяжется с Вами в ближайшее время.
            </div>
        </div>
        <div class="f-z_12">*Услуга по разработке дизайн-проекта предоставляется бесплатно при 100% оплате кухни под заказ (базовая стоимость составляет 4 000 рублей). Подробности у менеджеров.</div>
    </div>
</div>



