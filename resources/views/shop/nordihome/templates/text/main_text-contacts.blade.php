<!--template:Главная - контакты блок-->
@php
    /**
    * TextWidget::class - string
    * $widget->caption - string
    * $widget->description - string
    * $widget->image - Photo::class
    * $widget->icon - Photo::class
    * TextWidgetItem:class
    * $widget->items - Arraible
    * $widget->itemBySlug(string)?: TextWidgetItem
    * $item->caption -
    * $item->description -
    * $item->text - text (форматируемый текст)
 */
    /** @var \App\Modules\Page\Entity\Widgets\TextWidget $widget */
@endphp
<div id="contacts-tab"></div>
<div class="heading f-w_600 f-z_23 m-b_20">КОНТАКТЫ</div>
<div class="m-b_30">Мы всегда рады ответить на все Ваши вопросы,<br>принять пожелания и предложения по работе нашего
    сервиса
</div>
<div class="contacts-items">
    <div class="item">
        <div class="item-img"><img src="/images/nordihome/icon-f-phone.svg" alt="Контакты Евроикея"></div>
        <div class="item-text">
            <div class="f-w_300 f-z_23 t-a_center m-b_10">Телефон/Мессенджеры</div>
            <div class="t-a_center f-z_23 m-b_20"><a href="tel:88007008179" data-type="1">8 (800) 700-81-79</a></div>
            <div class="item-social m-t_10">
                <div class="link t-t_uppercase f-w_600">нажми</div>
                <a href="https://wa.me/+79062108505?text=Здравствуйте!%20Хочу%20мебель%20из%20ИКЕА!" class="link"
                   data-type="4"><img src="/images/nordihome/whatsapp-logo.png" alt="Лого востап"></a>
                <a href="https://t.me/NordiHomeBot" class="link" data-type="2"><img
                        src="/images/nordihome/telegram-logo.png" alt="Лого телеграм"></a>
            </div>
        </div>
    </div>
    <div class="item">
        <div class="item-img"><img src="/images/nordihome/icon-f-shop.svg" alt="Контакты Евроикея"></div>
        <div class="item-text">
            <div class="f-w_300 f-z_23 t-a_center">Другие площадки</div>
            <div class="item-social m-t_10">
                <div class="link"><b>@nordihome.ru</b></div>
                <a href="https://vk.com/nordihome" class="link" data-type="3"><img src="/images/nordihome/logo-vk.png"
                                                                                   alt="Лого вконтакте"></a>
                <a href="https://t.me/nordi_home" class="link" data-type="2"><img
                        src="/images/nordihome/telegram-logo.png" alt="Лого телеграм"></a>
                <a href="https://www.avito.ru/brands/nordihome.ru/all?sellerId=77c3371d8da225e083f29aea5b416174"
                   target="_blank" data-type="6"><img src="/images/nordihome/avito-logo.png" alt="Лого авито"
                                                      class="link"></a>
            </div>
        </div>
    </div>
    <div class="item">
        <div class="item-img"><img src="/images/nordihome/icon-f-mail.svg" alt="Контакты Евроикея"></div>
        <div class="item-text">
            <div class="f-w_300 f-z_16 t-a_center">По общим вопросам</div>
            <div class="item-mail">
                <a href="mailto:info@nordihome.ru" data-type="5">info@nordihome.ru</a>
                <div class="f-z_16">По вопросам сотрудничества</div>
                <a href="mailto:partnership@nordihome.ru">partnership@nordihome.ru</a>
            </div>
        </div>
    </div>
</div>


