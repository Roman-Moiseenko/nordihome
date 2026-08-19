(function () {

    const galleryItems = window.productGalleryData;
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

    const reviewGalleryData = window.reviewGalleryData;
    $('.review-image').on('click', function (e) {
        e.preventDefault();
        const currentIndex = $(this).data('index') || 0;

        Fancybox.show(reviewGalleryData, {
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


})();
