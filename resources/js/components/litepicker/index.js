import Dayjs from "dayjs";
window.dayjs = Dayjs;

(function () {
    "use strict";

    // Litepicker
    $(".datepicker").each(function () {
        let options = {
            autoApply: true,
            singleMode: false,
            numberOfColumns: 2,
            numberOfMonths: 2,
            showWeekNumbers: true,
            format: "DD-MM-YYYY",
            lang: 'ru-RU',
            dropdowns: {
                minYear: 1940,
                maxYear: (new Date()).getFullYear() + 2,
                months: true,
                years: true,
            },
            //buttonText:
        };

        if ($(this).data("single-mode")) {
            options.singleMode = true;
            options.numberOfColumns = 1;
            options.numberOfMonths = 1;
        }

        if ($(this).data("format")) {
            options.format = $(this).data("format");
        }

        if (!$(this).val()) {
            /*let date = dayjs().format(options.format);
            date += !options.singleMode
                ? " - " + dayjs().add(1, "month").format(options.format)
                : "";
            $(this).val(date);*/
        }

        new Litepicker({
            element: this,
            ...options,
        });
    });
})();
