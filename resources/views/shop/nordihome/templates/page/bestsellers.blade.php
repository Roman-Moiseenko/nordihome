<!--template:Страница Хиты продаж -->
@extends('shop.nordihome.layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $page->name }}</h1>
        <div class="page-products-hit">
            <p>Здесь должен быть топ-10 товаров из ИКЕА, но мы с клиентами и командой НОРДИ ХОУМ не смогли определиться. Поэтому создали топ-11. На самом деле, хитов еще больше, но эти – наши фавориты! Они универсальны, имеют отличное качество и помогают создать стильный интерьер в любом доме.</p>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6 info">
                        <h3>1. КОМОД MALM</h3>
                        <div class="text">
                            <p>Стильный и компактный комод с несколькими просторными ящиками, идеально подходящий для хранения одежды и аксессуаров.</p><p>Современный дизайн и легкость в уходе делают его идеальным выбором для любой спальни.</p>
                        </div>
                        <a href="/?s=MALM&amp;post_type=product&amp;dgwt_wcas=1" class="d-block">Смотреть всю серию</a>
                    </div>
                    <div class="col-lg-6 ">
                        <img src="/wp-content/uploads/2025/04/page-hit-01-min.jpg" alt="КОМОД MALM ikea">
                    </div>
                </div>
            </div>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6 product-hit-item-order-2">
                        <img src="/wp-content/uploads/2025/04/page-hit-02-min.jpg" alt="КРЕСЛО ОФИСНОЕ MARKUS ikea">
                    </div>
                    <div class="col-lg-6 bg-515151 info">
                        <h3>2. КРЕСЛО ОФИСНОЕ MARKUS</h3>
                        <div class="text">
                            <p>Обеспечивает комфорт и поддержку в течение рабочего дня. Оно позволяет регулировать высоту и угол наклона, что гарантирует удобное положение.</p><p>Сетчатая спинка обеспечивает хорошую циркуляцию воздуха, а встроенная поясничная опора поддерживает спину. Безопасные ролики с тормозным механизмом удерживают кресло на месте.</p><p>Идеально подходит для долгих часов работы.</p>
                        </div>
                        <a href="/shop/markus/" class="d-block">Перейтик товару</a>
                    </div>
                </div>
            </div>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6 bg-orange info">
                        <h3>3. НАСТОЛЬНАЯ ЛАМПА BLIDVADER</h3>
                        <div class="text">
                            <p>Элегантная настольная лампа с прочным керамическим основанием, рамой цвета латуни и абажуром с видимыми тканевыми волокнами.</p><p>Благодаря классической форме она подходит для большинства стилей декора и всех комнат в доме.</p>
                        </div>
                        <a href="/shop/blidvader/" class="d-block">Перейтик товару</a>
                    </div>
                    <div class="col-lg-6">
                        <img src="/wp-content/uploads/2025/04/page-hit-03-min.jpg" alt="НАСТОЛЬНАЯ ЛАМПА BLIDVADER ikea">
                    </div>
                </div>
            </div>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6 product-hit-item-order-2">
                        <img src="/wp-content/uploads/2025/04/page-hit-04-min.jpg" alt="КРЕСЛО OSKARSHAMN ikea">
                    </div>
                    <div class="col-lg-6 info">
                        <h3>4. КРЕСЛО OSKARSHAMN</h3>
                        <div class="text">
                            <p>Комфортное кресло с классическим дизайном, выполненное из качественных материалов. </p><p>Отлично впишется в гостиную или кабинет, даря приятные моменты отдыха.</p>
                        </div>
                        <a href="/shop/oskarshamn/" class="d-block">Перейтик товару</a>
                    </div>
                </div>
            </div>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6 info">
                        <h3>5. СТУЛ FROSVI</h3>
                        <div class="text">
                            <p>Стильный складной стул, который можно использовать как в столовой, так и в зоне отдыха.</p>
                        </div>
                        <a href="/?s=FROSVI&amp;post_type=product&amp;dgwt_wcas=1" class="d-block">Смотреть всю серию</a>
                    </div>
                    <div class="col-lg-6">
                        <img src="/wp-content/uploads/2025/04/page-hit-05-min.jpg" alt="СТУЛ FROSVI ikea">
                    </div>
                </div>
            </div>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6 product-hit-item-order-2">
                        <img src="/wp-content/uploads/2025/04/page-hit-06-min.jpg" alt="ЗЕРКАЛО IKORNNES ikea">
                    </div>
                    <div class="col-lg-6 bg-orange info">
                        <h3>6. ЗЕРКАЛО IKORNNES</h3>
                        <div class="text">
                            <p>Эстетичное зеркало с рамкой, которое добавляет глубины и света в любое помещение. Подходит для коридора или спальни, помогает создать атмосферу стиля.</p>
                        </div>
                        <a href="/shop/ikornnes/" class="d-block">Перейти к товару</a>
                    </div>
                </div>
            </div>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6 info">
                        <h3>7. СЕРИЯ KALLAX</h3>
                        <div class="text">
                            <p>Полки и ящики из этой серии могут использоваться как самостоятельные элементы или комбинироваться друг с другом, создавая уникальные конфигурации.</p><p>Изготавливаются из качественных материалов, легко собираются и доступны в различных цветах и размерах. Подходят для использования в гостиных, спальнях, офисах и детских комнатах.</p>
                        </div>
                        <a href="/?s=KALLAX&amp;post_type=product&amp;dgwt_wcas=1" class="d-block">Смотреть всю серию</a>
                    </div>
                    <div class="col-lg-6 product-hit-item-order-2">
                        <img src="/wp-content/uploads/2025/04/page-hit-07-min.jpg" alt="СЕРИЯ KALLAX ikea">
                    </div>
                </div>
            </div>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6">
                        <img src="/wp-content/uploads/2025/04/page-hit-08-min.jpg" alt="ТОРШЕР RINGSTA/SKAFTET ikea">
                    </div>
                    <div class="col-lg-6 bg-515151 info">
                        <h3>8. ТОРШЕР RINGSTA/SKAFTET</h3>
                        <div class="text">
                            <p>Стильный торшер, обеспечивающий мягкое освещение и скандинавский шарм. </p><p>Идеально подходит для чтения или создания уютного уголка в комнате.</p>
                        </div>
                        <a href="/shop/ringsta-skaftet-2/" class="d-block">Перейти к товару</a>
                    </div>
                </div>
            </div>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6 bg-orange info">
                        <h3>9. ДИВАН PARUP</h3>
                        <div class="text">
                            <p>Удобный диван с превосходной поддержкой, который отлично подходит для небольших пространств. Обивка из прочного материала легко чистится, а стильный минимализм гармонично впишется в любой интерьер.</p>
                        </div>
                        <a href="/?s=PARUP&amp;post_type=product&amp;dgwt_wcas=1" class="d-block">Смотреть всю серию</a>
                    </div>
                    <div class="col-lg-6">
                        <img src="/wp-content/uploads/2025/04/page-hit-09-min.jpg" alt="ДИВАН PARUP ikea">
                    </div>
                </div>
            </div>
            <div class="product-hit-item">
                <div class="row">
                    <div class="col-lg-6 product-hit-item-order-2">
                        <img src="/wp-content/uploads/2025/04/page-hit-10-min.jpg" alt="СТОЛИК ЖУРНАЛЬНЫЙ GLADOM ikea">
                    </div>
                    <div class="col-lg-6 info">
                        <h3>10. СТОЛИК ЖУРНАЛЬНЫЙ GLADOM</h3>
                        <div class="text">
                            <p>Многофункциональный журнальный столик — незаменимый элемент для вашей гостиной. Съёмный поднос можно использовать для сервировки.</p>
                        </div>
                        <a href="/?s=GLADOM&amp;post_type=product&amp;dgwt_wcas=1" class="d-block">Смотреть всю серию</a>
                    </div>
                </div>
            </div>
            <div class="product-hit-item last">
                <div class="row">
                    <div class="col-lg-6 info">
                        <h3>11. МОДУЛЬНАЯ СИСТЕМА PAX</h3>
                        <div class="text">
                            <p>Гибкая система хранения, позволяющая создать шкаф по вашим размерам и желаниям.</p>
                        </div>
                        <a href="/?s=PAX&amp;post_type=product&amp;dgwt_wcas=1" class="d-block">Смотреть всю серию</a>
                        <img src="/wp-content/uploads/2025/04/page-hit-12-min.jpg" alt="МОДУЛЬНАЯ СИСТЕМА PAX ikea" class="last">
                    </div>
                    <div class="col-lg-6">
                        <img src="/wp-content/uploads/2025/04/page-hit-11-min.jpg" alt="МОДУЛЬНАЯ СИСТЕМА PAX ikea">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
        {!! $page->text !!}
    </div>

@endsection
