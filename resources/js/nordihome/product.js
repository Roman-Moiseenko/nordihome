(function () {

    //Аккордион
    $('.accordion_1 .accordion-heading').on('click', function (){
        let thisContentBlock = $(this).parent().find('.accordion-text');
        if(thisContentBlock.hasClass('active')) {
            thisContentBlock.removeClass('active')
        }
        else {
            thisContentBlock.addClass('active')
        }
    });
    var acc = document.getElementsByClassName("accordion-heading");
    var i;
    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");

        });
    }


    //FancyBox

    const galleryItems = window.productGalleryData;
    console.log(galleryItems)
    // Клик по главному изображению – открываем Fancybox
    $('#main-image-product').on('click', function (e) {
        e.preventDefault();
        const currentIndex = $('#main-image-product').data('index') || 0;

        Fancybox.show(galleryItems, {
            startIndex: currentIndex,

            infinite: true,
            Thumbs: {
                autoStart: false,
            },
            caption: function (fancybox, slide) {
                return slide.caption || '';
            },
        });
    });
    let main = $('main');
    /** СТРАНИЦА ТОВАРА **/
    if (main.hasClass('product-page')) {
        let sliderImages = $('.slider-image-product');
        let mainImage = $('#main-image-product');
        sliderImages.on('mouseover', function () {
            mainImage.attr('src', $(this).data('image'));
            mainImage.attr('data-index', $(this).data('index'));
        });
    }

})();



