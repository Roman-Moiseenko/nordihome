(function ($, window, document) {
    'use strict';

    let AnalyticsTracker = {

        config: {
            exitUrl: '/analytics/record-exit',
            actionUrl: '/analytics/track-action',
            pageViewId: null,
            uuid: null,
        },

        startTime: Date.now(),
        maxScrollDepth: 0,
        exitSent: false,

        init: function (options) {
            $.extend(this.config, options || {});

            this.bindScroll();
            this.bindExit();
            this.bindActions();

            // Для дебага — вывести конфиг
            if (window.console && window.ANALYTICS_DEBUG) {
                console.log('[Analytics] Initialized', this.config);
            }
        },

        /**
         * Отслеживание глубины прокрутки (только локально, отправка при уходе).
         */
        bindScroll: function () {
            var self = this;

            $(window).on('scroll', function () {
                var docHeight = $(document).height();
                var winHeight = $(window).height();
                var scrollTop = $(window).scrollTop();

                if (docHeight <= winHeight) return;

                var depth = Math.round((scrollTop + winHeight) / docHeight * 100);
                if (depth > self.maxScrollDepth) {
                    self.maxScrollDepth = depth;
                }
            });
        },

        /**
         * Отслеживание ухода со страницы.
         */
        bindExit: function () {
            var self = this;

            // Современный способ — visibilitychange
            $(document).on('visibilitychange', function () {
                if (document.visibilityState === 'hidden') {
                    self.sendExit('visibility_hidden');
                }
            });

            // Fallback для Safari / старых браузеров
            $(window).on('pagehide', function () {
                self.sendExit('pagehide');
            });
        },

        /**
         * Делегирование клика по элементам с data-analytics-action.
         */
        bindActions: function () {
            var self = this;

            $(document).on('click', '[data-analytics-action]', function () {
                var $el = $(this);
                var actionType = $el.data('analytics-action');
                var entityType = $el.data('entity-type') || null;
                var entityId = $el.data('entity-id') || null;
                var payload = $el.data('analytics-payload') || null;

                self.trackAction(actionType, entityType, entityId, payload);
            });
        },

        /**
         * Отправка данных о действии.
         */
        trackAction: function (actionType, entityType, entityId, payload) {
            var data = {
                actionType: actionType,
                entityType: entityType,
                entityId: entityId,
                pageViewId: this.config.pageViewId,
                payload: payload,
            };

            this.send(this.config.actionUrl, data);
        },

        /**
         * CSRF-токен из meta (для POST-запросов, если маршрут не в $except).
         */
        csrfToken: function () {
            var el = document.querySelector('meta[name="csrf-token"]');
            return el ? el.getAttribute('content') : '';
        },

        /**
         * Отправка данных о выходе со страницы (duration).
         */
        sendExit: function (reason) {
            if (this.exitSent) {
                return;
            }

            this.exitSent = true;

            var data = {
                duration: Math.round((Date.now() - this.startTime) / 1000),
                pageViewId: this.config.pageViewId,
                scrollDepth: this.maxScrollDepth,
                reason: reason || 'unknown',
            };
            var body = JSON.stringify(data);

            // fetch + keepalive — надёжно отправляет при закрытии вкладки,
            // отправляет cookies и корректный Content-Type: application/json.
            if (window.fetch) {
                try {
                    window.fetch(this.config.exitUrl, {
                        method: 'POST',
                        keepalive: true,
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken(),
                        },
                        body: body,
                    });
                    return;
                } catch (e) {
                    // переходим к fallback ниже
                }
            }

            // Fallback: sendBeacon (form-urlencoded — Laravel парсит надёжно).
            if (navigator.sendBeacon) {
                var params = new URLSearchParams();
                params.append('duration', data.duration);
                params.append('pageViewId', data.pageViewId);
                params.append('scrollDepth', data.scrollDepth);
                params.append('reason', data.reason);
                navigator.sendBeacon(this.config.exitUrl, params);
                return;
            }

            // Последний fallback — jQuery POST.
            this.send(this.config.exitUrl, data);
        },

        /**
         * Обычный POST через jQuery (для действий и fallback).
         */
        send: function (url, data) {
            return $.ajax({
                url: url,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                async: true,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken(),
                },
            }).fail(function (xhr) {
                if (window.console) {
                    console.error('[Analytics] POST failed', url, xhr.status, xhr.responseText);
                }
            });
        },
    };

    // Экспорт в глобальную область
    window.AnalyticsTracker = AnalyticsTracker;

})(jQuery, window, document);
