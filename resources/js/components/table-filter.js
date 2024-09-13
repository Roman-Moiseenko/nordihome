(function () {
    "use strict";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('.table-filter')
        .each(function () {
            let clearFilter = $(this).find("#clear-filter");
            clearFilter.on('click', function () {
                window.location.href = window.location.href.split("?")[0];
            });
        });
})();
