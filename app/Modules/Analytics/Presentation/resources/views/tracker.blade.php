<script>
    window.ANALYTICS_DEBUG = {{ config('app.debug') ? 'true' : 'false' }};
    window.ANALYTICS_SEARCH_CLICK_URL = @json(route('analytics.track-search-click'));

    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.AnalyticsTracker === 'undefined') {
            return;
        }

        window.AnalyticsTracker.init({
            pageViewId: @json($analyticsPageViewId ?? null),
            uuid: @json($analyticsUuid ?? null),
            exitUrl: @json(route('analytics.record-exit')),
            actionUrl: @json(route('analytics.track-action')),
        });
    });
</script>
