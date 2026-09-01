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
    /** @var \App\Modules\Content\Entity\Widgets\TextWidget $widget */
@endphp
<div id="contacts-tab"></div>
<div class="heading f-w_600 f-z_23 m-b_20">КОНТАКТЫ</div>
<div class="m-b_30">Мы всегда рады ответить на все Ваши вопросы,<br>принять пожелания и предложения по работе нашего
    сервиса
</div>
<div class="contacts-items">
    <div class="item">
        <div class="item-img"><img src="/images/nordihome/icon-f-phone.svg" alt="Контакты Норди Хоум"></div>
        <div class="item-text">
            <div class="f-w_300 f-z_23 t-a_center m-b_10">Телефон/Мессенджеры</div>
            <div class="t-a_center f-z_23 m-b_20">
                @if(isset($contacts['phone']))
                    <a href="{{ $contacts['phone']->url }}" class="link">{{ phone($contacts['phone']->url) }}</a>
                @endif
            </div>
            <div class="item-social m-t_10">
                <div class="link t-t_uppercase f-w_600">нажми</div>
                <!-- <a href="https://wa.me/+79062108505?text=Здравствуйте!%20Хочу%20мебель%20из%20ИКЕА!" data-type="4"><img src="/wp-content/themes/euroikea/images/whatsapp-logo.png" alt="Лого востап"></a> -->
                @if(isset($contacts['telegram_bot']))
                    <a href="{{ $contacts['telegram_bot']->url }}" class="link" target="_blank"><img src="/uploads/gallery/7/telegram-logo.png" alt="Лого телеграм"></a>
                @endif
                @if(isset($contacts['max_bot_1']))
                <a href="{{ $contacts['max_bot_1']->url }}" class="link" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 131 42" fill="none" width="129" height="40"><path fill="#000000" d="M21.47 41.88c-4.11 0-6.02-.6-9.34-3-2.1 2.7-8.75 4.81-9.04 1.2 0-2.71-.6-5-1.28-7.5C1 29.5.08 26.07.08 21.1.08 9.23 9.82.3 21.36.3c11.55 0 20.6 9.37 20.6 20.91a20.6 20.6 0 0 1-20.49 20.67Zm.17-31.32c-5.62-.29-10 3.6-10.97 9.7-.8 5.05.62 11.2 1.83 11.52.58.14 2.04-1.04 2.95-1.95a10.4 10.4 0 0 0 5.08 1.81 10.7 10.7 0 0 0 11.19-9.97 10.7 10.7 0 0 0-10.08-11.1Z"></path><path fill="#000000" d="M60.3 32.15h-4.44v-21h7.23l4.84 14.41h.65l5.05-14.41h7.07v21h-4.45v-15.6h-.64l-5.5 15.6H66.2l-5.25-15.6h-.65v15.6ZM94.59 32.55c-1.97 0-3.73-.46-5.3-1.37a9.99 9.99 0 0 1-3.67-3.88 12.15 12.15 0 0 1-1.29-5.65c0-2.1.43-3.98 1.3-5.62a9.63 9.63 0 0 1 3.67-3.88 10.04 10.04 0 0 1 5.29-1.4c1.75 0 3.3.37 4.64 1.12 1.35.73 2.45 1.62 3.31 2.67l.97-3.4H107v21h-3.47l-.97-3.39a11.45 11.45 0 0 1-3.32 2.7 9.62 9.62 0 0 1-4.64 1.1Zm1.13-4.16c1.97 0 3.55-.62 4.77-1.86a6.7 6.7 0 0 0 1.85-4.88c0-2-.62-3.61-1.85-4.85a6.3 6.3 0 0 0-4.77-1.9c-1.94 0-3.51.63-4.72 1.9a6.63 6.63 0 0 0-1.82 4.85c0 1.99.6 3.62 1.82 4.88a6.32 6.32 0 0 0 4.72 1.86ZM115.03 32.15h-5.25l6.66-10.75-5.9-10.25h5.26l3.91 7.06h.77l4.12-7.06h5.13l-5.9 9.97 6.67 11.03h-5.42l-4.48-7.96h-.77l-4.8 7.96Z"></path></svg></a>
                @endif
            </div>
        </div>
    </div>
    <div class="item">
        <div class="item-img"><img src="/images/nordihome/icon-f-shop.svg" alt="Контакты Норди Хоум"></div>
        <div class="item-text">
            <div class="f-w_300 f-z_23 t-a_center">Другие площадки</div>
            <div class="item-social m-t_10">
                <div class="link">
                    @if(isset($contacts['instagram']))
                        <b>{{ $contacts['instagram']->url }}</b>
                    @endif
                </div>
                @if(isset($contacts['vk']))
                    <a href="{{ $contacts['vk']->url }}" class="link" target="_blank"><img src="/images/nordihome/logo-vk.png" alt="Лого вконтакте"></a>
                @endif
                @if(isset($contacts['telegram']))
                    <a href="{{ $contacts['telegram']->url }}" class="link" target="_blank"><img src="/images/nordihome/telegram-logo.png" alt="Лого телеграм"></a>
                @endif
                @if(isset($contacts['avito']))
                <a href="{{ $contacts['avito']->url }}"
                   target="_blank" ><img src="/images/nordihome/avito-logo.png" alt="Лого авито"></a>
                @endif
            </div>
        </div>
    </div>
    <div class="item">
        <div class="item-img"><img src="/images/nordihome/icon-f-mail.svg" alt="Контакты Евроикея"></div>
        <div class="item-text">
            <div class="f-w_300 f-z_16 t-a_center">По общим вопросам</div>
            <div class="item-mail">
                @if(isset($contacts['mail']))
                    <a href="{{ $contacts['mail']->url }}">{{ $contacts['mail']->name }}</a>
                @endif
                <div class="f-z_16">По вопросам сотрудничества</div>
                    @if(isset($contacts['mail_1']))
                        <a href="{{ $contacts['mail_1']->url }}">{{ $contacts['mail_1']->name }}</a>
                    @endif
                <div class="f-z_16">Отдел логистики</div>
                    @if(isset($contacts['mail_2']))
                        <a href="{{ $contacts['mail_2']->url }}">{{ $contacts['mail_2']->name }}</a>
                    @endif
                <div class="f-z_16">Претензии и спорные вопросы</div>
                    @if(isset($contacts['mail_3']))
                        <a href="{{ $contacts['mail_3']->url }}">{{ $contacts['mail_3']->name }}</a>
                    @endif
            </div>
        </div>
    </div>
</div>


