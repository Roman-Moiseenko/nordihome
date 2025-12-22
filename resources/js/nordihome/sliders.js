import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";

    //Карусели
    let optionsSliderBase = {
        rtl: false,
        startPosition: 0,
        items: 1,
        autoplay: true, //
        smartSpeed: 1500, //Время движения слайда
        autoplayTimeout: 4000, //Время смены слайда
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        margin: 10,
        loop: true,
        dots: true,
        nav: true,
        navText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
        singleItem: true,
        transitionStyle: "fade",
        touchDrag: true,
        mouseDrag: true,

    };
    if (document.getElementById('slider-payment') !== null) {
        let sliderPayment = $('#slider-payment');
        let optionsSliderPayment = {...optionsSliderBase};
        optionsSliderPayment.autoplay = false;
        optionsSliderPayment.items = 5;
        optionsSliderPayment.loop = false;
        optionsSliderPayment.nav = false;

        sliderPayment.owlCarousel(optionsSliderPayment);
        sliderPayment.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderPayment.trigger('next.owl');
            } else {
                sliderPayment.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }
    if (document.getElementById('main-slider01') !== null) {
        let sliderPayment = $('#main-slider01');
        sliderPayment.owlCarousel(optionsSliderBase);
        sliderPayment.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderPayment.trigger('next.owl');
            } else {
                sliderPayment.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }
    if (document.getElementById('slider-main-specials') !== null) {
        let sliderPayment = $('#slider-main-specials');
        sliderPayment.owlCarousel(optionsSliderBase);
        sliderPayment.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderPayment.trigger('next.owl');
            } else {
                sliderPayment.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }
    if (document.getElementById('slider-main-interesting') !== null) {
        let sliderPayment = $('#slider-main-interesting');
        sliderPayment.owlCarousel(optionsSliderBase);
        sliderPayment.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderPayment.trigger('next.owl');
            } else {
                sliderPayment.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }
    if (document.getElementById('slider-delivery-company') !== null) {
        let sliderDeliveryCompany = $('#slider-delivery-company');
        let optionsSliderDelivery = {...optionsSliderBase};
        optionsSliderDelivery.autoplay = false;
        optionsSliderDelivery.items = 5;
        optionsSliderDelivery.loop = false;
        optionsSliderDelivery.nav = false;
        optionsSliderDelivery.dots = false;
        sliderDeliveryCompany.owlCarousel(optionsSliderDelivery);
        sliderDeliveryCompany.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderDeliveryCompany.trigger('next.owl');
            } else {
                sliderDeliveryCompany.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }
    if (document.getElementById('main-slider-reviews') !== null) {
        let sliderOldReviews = $('#main-slider-reviews');
        let optionsOldReviews = optionsSliderBase;
        optionsOldReviews.mouseDrag = true;
        optionsOldReviews.margin = 40;
        optionsOldReviews.responsive = { 0: {items: 1}, 576: {items: 2}, 768: {items: 3}, 991: {items: 4}, 1200: {items: 5}, 1400: {items: 6}};
        sliderOldReviews.owlCarousel(optionsOldReviews);
    }
    if (document.querySelectorAll('.slider-images-product') !== null) {
        let product_optionsSliderBase = optionsSliderBase;
        let slidersImagesProduct = $('.slider-images-product');
        slidersImagesProduct.each(function (element) {
            let sliderImagesProduct = $(this);
            let mouseScroll = sliderImagesProduct.data('mouse-scroll');
            let responsive = sliderImagesProduct.data('responsive');
            if (responsive === undefined || responsive.length !== 3) responsive = [3, 6, 9];
            product_optionsSliderBase.responsive = {0: {items: responsive[0]}, 576: {items: responsive[1]}, 991: {items: responsive[2]}};
            product_optionsSliderBase.margin = 0;
            sliderImagesProduct.owlCarousel(product_optionsSliderBase);
            if (mouseScroll !== 0) {
                sliderImagesProduct.on('mousewheel', '.owl-stage', function (e) {
                    if (e.originalEvent.deltaY > 0) {
                        sliderImagesProduct.trigger('next.owl');
                    } else {
                        sliderImagesProduct.trigger('prev.owl');
                    }
                    e.preventDefault();
                });
            }
        });
    }
    if (document.getElementByClassName('main-slider01') !== null) {
        let sliderPayment = $('#main-slider01');
        sliderPayment.owlCarousel(optionsSliderBase);
        sliderPayment.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderPayment.trigger('next.owl');
            } else {
                sliderPayment.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }
    if (document.getElementById('slider-old-catalog') !== null) {
        let sliderOldCatalog = $('#slider-old-catalog');
        let optionsOldCatalog = optionsSliderBase;
        optionsOldCatalog.mouseDrag = true;
        optionsOldCatalog.dots = true;
        optionsOldCatalog.nav = false;
        optionsOldCatalog.responsive = { 0: {items: 1}};
        sliderOldCatalog.owlCarousel(optionsOldCatalog);
    }



/*

    if (document.getElementById('slider-images-product') !== null) {
        let sliderImagesProduct = $('#slider-images-product');

        let product_optionsSliderBase = optionsSliderBase;
        let responsive = sliderImagesProduct.data('responsive');
        if (responsive === undefined || responsive.length !== 3) responsive = [3, 6, 9];
        product_optionsSliderBase.responsive = {0: {items: responsive[0]}, 576: {items: responsive[1]}, 991: {items: responsive[2]}};
        product_optionsSliderBase.margin = 0;
        sliderImagesProduct.owlCarousel(product_optionsSliderBase);
        sliderImagesProduct.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderImagesProduct.trigger('next.owl');
            } else {
                sliderImagesProduct.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }

 */
})();

