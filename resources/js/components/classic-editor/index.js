(function () {
    "use strict";

    $(".editor").each(function () {
        const el = this;
        ClassicEditor
            .create(el, {
                placeholder: 'Описание',
                htmlEmbed: {
                    showPreviews: true
                }
            })
            .catch((error) => {
            console.error(error);
        });
    });
})();
