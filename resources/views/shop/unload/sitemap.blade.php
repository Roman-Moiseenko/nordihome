<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
    @foreach ($pages as $page)<url>
        <loc>{{ $page['url'] }}</loc>
        <lastmod>{{ $page['date'] }}</lastmod>
        <changefreq>{{ $page['changefreq'] }}</changefreq>
        <priority>0.8</priority>
    </url>@endforeach
</urlset>
