<!--template:Главная - яндекс карта-->
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
<div class="container f-w_600 t-a_center m-b_10">Мы находимся по адресу: г. Калининград, Советский проспект 103 А,
    корпус 1.
</div>
<div class="block-map">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 padding_0">
                <div class="map-yandex" id="map-yandex"></div>
                <script>
                    let ok = false;
                    window.addEventListener('scroll', function () {
                        if (ok === false) {
                            ok = true;
                            setTimeout(() => {
                                let script = document.createElement('script');
                                script.src = 'https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A80f22b8c4fd9830e883d991e3b5cdb798ae031257cedc84debc14db334a49eff&amp;width=100%&amp;height=400&amp;lang=ru_RU&amp;scroll=false';
                                document.getElementById('map-yandex').replaceWith(script);
                            }, 2000)
                        }
                    });
                </script>
            </div>
        </div>
    </div>


</div>
