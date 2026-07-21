(function () {

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
})();



