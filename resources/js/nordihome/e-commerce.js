import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";
//Слушатели отлавливающие события
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        const impressions = $('.e-impressions')
        if (impressions.length !== 0) {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.5 // Доля видимости элемента
            };
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) { // Элемент появился на экране
                        const targetElement = entry.target;
                        let product_id = targetElement.getAttribute('data-product')
                        eCommerceAjax('impressions', product_id)
                        observer.unobserve(targetElement);
                    }
                });
            }, observerOptions);

            impressions.each(impression => {
                observer.observe(impressions[impression]);
            });
        }

        $('.e-click').on('click', function (item) {
            item.preventDefault();
            eCommerceAjax('click', $(this).data('product'), $(this).attr('href'))
        })
        let detail = $('.e-detail')

        if (detail.length > 0) {
            function eDetail() {
                let product_id = detail[0].getAttribute('data-product')
                eCommerceAjax('detail', product_id);
                $(window).off('scroll', eDetail)
            }
            $(window).on('scroll', eDetail)
        }

        let adds = $('.e-add')
        if (adds.length > 0) {
            adds.on('click', function () {
                let item = $(this)
                eCommerceAjax('add', item.data('product'))
            })
        }
        let removes = $('.e-remove')
        if (removes.length > 0) {
            removes.on('click', function () {
                let item = $(this)
                eCommerceAjax('remove', item.data('product'), null, item.data('quantity'))
            })
        }

        let clears = $('.e-clear')
        if (clears.length > 0) {
            clears.on('click', function () {
                let item = $(this)
                eCommerceAjax('clear', item.data('product'))
            })
        }
        let purchases = $('.e-purchase')
        if (purchases.length > 0) {
            purchases.on('click', function () {
                let item = $(this)
                eCommerceAjax('purchase', item.data('product'))
            })
        }

        let buyclicks = $('.e-buy-click')
        if (buyclicks.length > 0) {
            buyclicks.on('click', function () {
                let item = $(this)
                eCommerceAjax('add', item.data('product'))
                eCommerceAjax('purchase', item.data('product'))
            })
        }
    });

    window.addEventListener('e-cart', event => {
        let data = event.detail
        eCommerceAjax(data.e_type, data.product_id, null, data.quantity)
    })
    window.addEventListener('e-order', event => {

        let data = event.detail
        console.log('e-order', data)
        eCommerceAjax('purchase', data)
    })
    function eCommerceAjax(eType, eId, href = null, quantity = 1) {

        $.post('/e-commerce', {e_id: eId, e_type: eType, quantity: quantity}, function (data) {
            console.log(data)
            window.dataLayer.push(data)
            if (href !== null) window.location = href
        });

    }

})();
