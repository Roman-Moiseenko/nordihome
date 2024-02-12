import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";

    //Карусели
    let optionsSliderBase = {
        rtl: false,
        startPosition: 0,
        items: 1,
        autoplay: false, //
        smartSpeed: 1500, //Время движения слайда
        autoplayTimeout: 1000, //Время смены слайда
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        margin: 10,
        loop: false,
        dots: false,
        nav: true,
        navText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
        singleItem: true,
        transitionStyle: "fade",
        touchDrag: true,
        mouseDrag: false,
        responsive: {
            0: {
                items: 1,
                smartSpeed: 500
            },
            576: {
                items: 2,
                smartSpeed: 500
            },
            991: {
                items: 6,
                smartSpeed: 500
            },
        }
    };
    if (document.getElementById('slider-payment') !== null) {
        let sliderPayment = $('#slider-payment');
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
        sliderDeliveryCompany.owlCarousel(optionsSliderBase);
        sliderDeliveryCompany.on('mousewheel', '.owl-stage', function (e) {
            if (e.originalEvent.deltaY > 0) {
                sliderDeliveryCompany.trigger('next.owl');
            } else {
                sliderDeliveryCompany.trigger('prev.owl');
            }
            e.preventDefault();
        });
    }
    if (document.getElementById('slider-images-product') !== null) {
        let sliderImagesProduct = $('#slider-images-product');
        let product_optionsSliderBase = optionsSliderBase;

        product_optionsSliderBase.responsive = {0: {items: 9}};
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

})();

