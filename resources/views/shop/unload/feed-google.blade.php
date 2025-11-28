<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
    <channel>
        <title>{{ $info['name'] }}</title>
        <description>{{ $info['description'] }}</description>
        <link>{{ $info['url'] }}</link>
        @foreach($products as $product)
        <item>
            <g:id>{{ $product['id'] }}</g:id>
            <g:title>{{ $product['name'] }}</g:title>
            <g:description>
                <![CDATA[ {!! $product['description'] !!} ]]>
            </g:description>
            <g:link>{{ $product['url'] }}</g:link>
            @if(count($product['images']) > 0)<g:image_link>{{ $product['images'][0] }}</g:image_link> @endif
            <g:availability>{{ $product['store'] ? 'in_store' : 'preorder' }}</g:availability>
            @if(!$product['store'])<g:availability_date>{{ $date }}</g:availability_date> @endif
            <g:price>{{ $product['price'] }} RUB</g:price>
            <g:condition>new</g:condition>
        </item>
        @endforeach
    </channel>
</rss>
