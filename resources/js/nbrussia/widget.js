import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";
    /**
     * Widget  group-slider-3.blade.php
     * Класс css .widget-home-3-group
     * slider-best-group
     */
    let widgetHome3Group = $('.widget-home-3-group')
    if (widgetHome3Group.length) {
        //Переключение групп
        let arrayLi = $('.caption-group>li')
        let arrayDiv = $('.slider-group>div')
        arrayLi.each(function () {
            let item = $(this)
            item.on('click', function () {
                arrayLi.removeClass('active')
                item.addClass('active')
                arrayDiv.each(function () {
                    if (!$(this).hasClass('hidden')) $(this).addClass('hidden')
                    if ($(this).attr('id') === item.data('id')) $(this).removeClass('hidden')
                })

            })
        })
        //Слайдер для групп
        let optionsSliderBase = {
            rtl: false,
            startPosition: 0,
            items: 3,
            autoplay: false, //
            smartSpeed: 1500, //Время движения слайда
            autoplayTimeout: 1000, //Время смены слайда
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            margin: 10,
            loop: true,
            dots: false,
            nav: true,
            navText: ['<i class="fa-light fa-chevron-left"></i>', '<i class="fa-light fa-chevron-right"></i>'],
            singleItem: true,
            transitionStyle: "fade",
            touchDrag: false,
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
                    items: 3,
                    smartSpeed: 500
                },
            }
        };
        let sliderPayment = $('.slider-best-group');
        sliderPayment.each(function () {
            $(this).owlCarousel(optionsSliderBase);
            $(this).trigger('refresh.owl.carousel');
        })
    }
})();
