<div class="container-xl my-5">
    <h2 class="page-h2">Отзывы клиентов</h2>
    <div class="row justify-content-center">
        <div class="col-sm-6 col-md-6 col-lg-3">
            <a href="https://yandex.ru/profile/3616123262" target="_blank" class="main-rating-item">
                <div>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <span>4.7 из 5</span>
                </div>
                <img src="/images/pages/home/logo-yandex.svg" alt="Яндекс каталог Nordihome">
            </a>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-3">
            <a href="https://2gis.ru/kaliningrad/firm/70000001062771418" target="_blank" class="main-rating-item">
                <div>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <span>4.4 из 5</span>
                </div>
                <img src="/images/pages/home/logo-2gis.svg" alt="2гис Nordihome">
            </a>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-3">
            <a href="https://maps.app.goo.gl/3b2nQBqTNWPn2MBV6" target="_blank" class="main-rating-item">
                <div>
                    <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <span>5 из 5</span>
                </div>
                <img src="/images/pages/home/logo-google.svg" alt="Гугл карты Nordihome">
            </a>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-3">
            <a href="https://www.avito.ru/user/f5379553f3e3b6ac316ad9f43160852e/profile/all?sellerId=f5379553f3e3b6ac316ad9f43160852e"
               target="_blank"
               class="main-rating-item">
                <div>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <span>694 отзыва</span>
                </div>
                <img src="/images/pages/home/avito_logo.svg" alt="Лого Авито Nordihome">
            </a>
        </div>
    </div>
</div>
@php
    $path = public_path() . '/images/pages/home/reviews/';
    $files = array_map(function (string $item){
                $info = pathinfo($item);
                return $info['basename'];
            }, glob($path . '*'));
    //    echo json_encode($files);

@endphp
<div id="slider-old-reviews" class="owl-carousel owl-theme">
    @foreach($files as $file)
        <img src="/images/pages/home/reviews/{{ $file }}" style="border-radius: 10px;">
    @endforeach
</div>
