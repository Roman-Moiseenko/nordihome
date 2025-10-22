<!--template:Главная - рейтинг фирмы-->
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
    /** @var \App\Modules\Page\Entity\TextWidget $widget */
@endphp
<div class="main-reviews p-t_50 p-b_50" id="reviews-tab">
    <h2 class="t-t_uppercase t-a_center">{{ $widget->caption }}</h2>
    <div class="row justify-content-center">
        <div class="col-sm-6 col-md-6 col-lg-4">
            <a href="https://yandex.ru/profile/3616123262" target="_blank" class="main-rating-item">
                <ul class="star">
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li>4.7 из 5</li>
                </ul>
                <img src="https://nordihome.ru/wp-content/themes/euroikea/images/logo-yandex.svg" alt="Яндекс каталог Nordihome">
            </a>
        </div>
        <!-- <div class="col-sm-6 col-md-6 col-lg-3">
            <div class="main-rating-item">
                <ul class="star">
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li>4.4 из 5</li>
                </ul>
                <img src="/wp-content/themes/euroikea/images/logo-2gis.svg" alt="2гис Nordihome">
            </div>
        </div> -->
        <!-- <div class="col-sm-6 col-md-6 col-lg-3">
            <div class="main-rating-item">
                <ul class="star">
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li>5 из 5</li>
                </ul>
                <img src="/wp-content/themes/euroikea/images/logo-google.svg" alt="Гугл карты Nordihome">
            </div>
        </div> -->
        <div class="col-sm-6 col-md-6 col-lg-4">
            <a href="https://www.avito.ru/brands/nordihome.ru/all?sellerId=77c3371d8da225e083f29aea5b416174" target="_blank" class="main-rating-item">
                <ul class="star">
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li class="one"></li>
                    <li>1025 отзывов</li>
                </ul>
                <img src="https://nordihome.ru/wp-content/themes/euroikea/images/avito_logo.svg" alt="Лого Авито Nordihome">
            </a>
        </div>
    </div>
</div>
