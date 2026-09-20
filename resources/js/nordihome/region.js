import jQuery from "jquery";
import { Modal } from "bootstrap";
import regions from "./regions.js";

window.$ = jQuery;

(function () {
    "use strict";

    const COOKIE_REGION = "user_cookie_region";
    const COOKIE_TOWN = "user_cookie_town";
    const DEFAULT_CODE = 39;
    const DEFAULT_TOWN = "Калининград";
    const DADATA_URL = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address?ip=";

    function getCookie(name) {
        const matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([.$?*|{}()\[\]\\/+^])/g, "\\$1") + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
    }

    function findRegion(code) {
        return regions.find((region) => Number(region.code) === Number(code));
    }

    // Из tax_office (например, "3900") убираем последние два нуля -> "39"
    function regionCodeFromTaxOffice(taxOffice) {
        if (!taxOffice) return null;
        const str = String(taxOffice);
        if (str.length <= 2) return str;
        return str.slice(0, -2);
    }

    // Отсекаем ботов/краулеров, чтобы не тратить запросы к Dadata
    function isBot() {
        // Автоматизация и headless-браузеры (Selenium, Puppeteer, Playwright и т.п.)
        if (navigator.webdriver) return true;

        const ua = String(navigator.userAgent || "").toLowerCase();
        if (!ua) return false;

        const botPatterns = [
            "bot",
            "crawler",
            "spider",
            "slurp",
            "bingpreview",
            "facebookexternalhit",
            "facebot",
            "whatsapp",
            "telegrambot",
            "linkedinbot",
            "pinterestbot",
            "headlesschrome",
            "phantomjs",
            "puppeteer",
            "playwright",
            "selenium",
            "yandexbot",
            "googlebot",
            "bingbot",
            "baiduspider",
            "duckduckbot",
            "ahrefsbot",
            "semrushbot",
            "mj12bot",
            "dotbot",
            "uptimerobot",
            "pingdom",
        ];

        return botPatterns.some((pattern) => ua.includes(pattern));
    }

    // Подпись региона в шапке: приоритет у user_cookie_town, затем название региона
    function updateHeader(region) {
        const town = getCookie(COOKIE_TOWN);
        const label = town || (region ? region.name : null);
        if (label) {
            // Атрибутный селектор, т.к. в шапке два блока с id="code-region"
            // (мобильный и ПК), а $("#code-region") обновляет только первый
            $('[id="code-region"]').text(label);
        }
    }

    function fillRegionSelect(select, selectedCode) {
        select.empty();
        regions.forEach((region) => {
            select.append($("<option></option>").val(region.code).text(region.name));
        });
        select.val(String(selectedCode || DEFAULT_CODE));
    }

    function showRegionModal(selectedCode) {
        const modalEl = document.getElementById("region-popup");
        const select = $("#region-select");
        if (!modalEl || !select.length) return;
        fillRegionSelect(select, selectedCode);
        Modal.getOrCreateInstance(modalEl).show();
    }

    $(document).ready(function () {
        const settings = window.regionSettings || {};
        const modalEl = document.getElementById("region-popup");

        // previousCode — регион, активный до открытия окна (для решения о перезагрузке);
        // initialCode — регион, предвыбранный в окне (чтобы понять, менял ли его клиент).
        let previousCode = DEFAULT_CODE;
        let initialCode = DEFAULT_CODE;
        let resolved = false;

        function resolveRegion(finalRegion, town) {
            if (resolved) return;
            resolved = true;

            setCookie(COOKIE_REGION, finalRegion.code, 365);
            if (town) {
                setCookie(COOKIE_TOWN, town, 365);
            }
            updateHeader(finalRegion);

            if (modalEl) {
                Modal.getOrCreateInstance(modalEl).hide();
            }

            // Перезагружаем сайт только если регион реально изменился
            if (Number(finalRegion.code) !== Number(previousCode)) {
                location.reload();
            }
        }

        function resolveCancel() {
            if (resolved) return;

            // Ручное открытие (клик по шапке): отмена просто закрывает окно, ничего не меняем
            if (getCookie(COOKIE_REGION) !== undefined) {
                resolved = true;
                if (modalEl) Modal.getOrCreateInstance(modalEl).hide();
                return;
            }

            // Автоопределение при первом заходе: сохраняем регион по умолчанию (39)
            const fallback = findRegion(DEFAULT_CODE);
            const town = Number(initialCode) !== DEFAULT_CODE ? DEFAULT_TOWN : undefined;
            resolveRegion(fallback, town);
        }

        // Кнопка "Ок"
        $("#region-ok").on("click", function () {
            const selected = findRegion($("#region-select").val()) || findRegion(DEFAULT_CODE);

            // user_cookie_town пересохраняем только если регион действительно изменился
            const regionChanged = Number(selected.code) !== Number(initialCode);
            const town = regionChanged ? selected.name : undefined;

            resolveRegion(selected, town);
        });

        // Кнопка "Отмена"
        $("#region-cancel").on("click", resolveCancel);

        // Закрытие окна крестиком — обрабатываем так же, как "Отмену"
        if (modalEl) {
            $(modalEl).on("hidden.bs.modal", function () {
                resolveCancel();
            });
        }

        // Клик по блоку региона в шапке (мобильный и ПК) — открываем окно смены региона
        $(document).on("click", '[id="code-region"]', function () {
            const currentCode = getCookie(COOKIE_REGION) || DEFAULT_CODE;
            previousCode = Number(currentCode);
            initialCode = Number(currentCode);
            resolved = false;
            showRegionModal(currentCode);
        });

        // --- Автоопределение региона при первом заходе ---
        const existingRegion = getCookie(COOKIE_REGION);
        const existingTown = getCookie(COOKIE_TOWN);

        if (existingRegion !== undefined || existingTown !== undefined) {
            // Регион уже выбран — показываем его в шапке и не открываем модалку
            updateHeader(findRegion(existingRegion));
            return;
        }

        // Боты/краулеры: не определяем регион и не показываем окно выбора
        if (isBot()) {
            return;
        }

        if (!settings.token || !settings.ip) {
            return;
        }

        const select = $("#region-select");
        if (!select.length) return;

        // Определяем регион по IP через сервис Dadata
        fetch(DADATA_URL + encodeURIComponent(settings.ip), {
            method: "GET",
            mode: "cors",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": "Token " + settings.token
            }
        })
            .then((response) => response.json())
            .then((result) => {
                const location = result && result.location ? result.location : null;

                if (location && location.data) {
                    // Сохраняем город из поля city
                    if (location.data.city) {
                        setCookie(COOKIE_TOWN, location.data.city, 365);
                    }

                    const code = regionCodeFromTaxOffice(location.data.tax_office);
                    const detected = findRegion(code) || findRegion(DEFAULT_CODE);
                    initialCode = Number(detected.code);
                } else {
                    initialCode = DEFAULT_CODE;
                }
            })
            .catch(() => {
                initialCode = DEFAULT_CODE;
            })
            .finally(() => {
                showRegionModal(initialCode);
            });
    });
})();
