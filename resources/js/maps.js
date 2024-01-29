import jQuery from "jquery";

window.$ = jQuery;

(function () {
    "use strict";

    let mapBlock = $('#map');
    if (!mapBlock.length) return false;
    loadScript("https://api-maps.yandex.ru/2.1/?apikey=" + mapBlock.data('api') + "&lang=ru_RU", function () {
        ymaps.load(init);
    });

    function init() {
        if (mapBlock.length) {
            let map = new ymaps.Map(mapBlock.get(0), { //document.getElementById('map')
                center: [54.712149,  20.509589],
                zoom: 12
            }, {
                restrictMapArea: [
                    [54.256, 19.586],
                    [55.317, 22.975]
                ]
            });
            map.controls.remove('searchControl');
            map.controls.remove('trafficControl');
            map.controls.remove('geolocationControl');
            $.post(mapBlock.data('route'), {},//ajax запрос
                function (data) {
                    let  _points = data;//JSON.parse(data);
                    for (let i = 0; i < _points.length; i++) {
                        map.geoObjects.add(new ymaps.Placemark([_points[i].latitude, _points[i].longitude], {
                            iconContent: i + 1,
                            iconCaption: _points[i].iconCaption,
                            balloonContent: _points[i].balloonContent
                        }, {
                            preset: 'islands#blackIcon',
                            draggable: false
                        }));
                    }
                });
        }
    }

    function loadScript(url, callback) {
        let script = document.createElement("script");
        if (script.readyState) {  //IE
            script.onreadystatechange = function () {
                if (script.readyState === "loaded" || script.readyState === "complete") {
                    script.onreadystatechange = null;
                    callback();
                }
            };
        } else {  //Другие браузеры
            script.onload = function () {
                callback();
            };
        }
        script.src = url;
        document.getElementsByTagName("head")[0].appendChild(script);
    }
})();
