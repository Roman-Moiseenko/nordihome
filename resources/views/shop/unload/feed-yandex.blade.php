<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<yml_catalog date="{{ $date }}">
    <shop>
        <name>{{ $info['name'] }}</name>
        <company>{{ $info['company'] }}</company>
        <url>{{ $info['url'] }}</url>
        <platform>CRM Web39</platform>
        <version>0.9.09</version>
        <currencies>
            <currency id="RUR" rate="1"/>
        </currencies>
        <categories>
            @foreach($yml_categories as $category)
                @if(isset($category['parent']))
                    <category id="{{ $category['id'] }}" parentId="{{ $category['parent'] }}">{{ $category['name'] }}</category>
                @else
                    <category id="{{ $category['id'] }}">{{ $category['name'] }}</category>
                @endif
            @endforeach
        </categories>
        <offers>
            @foreach($products as $product)
                <offer id="{{ $product['id'] }}" available="true">
                    <name>{{ $product['name'] }}</name>
                    <description>
                        <![CDATA[ {!! $product['description'] !!} ]]>
                    </description>
                    @foreach($product['images'] as $image)
                    <picture>{{ $image }}</picture>
                    @endforeach
                    <url>{{ $product['url'] }}</url>
                    <price>{{ $product['price'] }}</price>
                    @if($info['preprice']) <oldprice>{{ $product['preprice'] }}</oldprice> @endif
                    <currencyId>RUR</currencyId>
                    <store>{{ $product['store'] }}</store>
                    <pickup>{{ $product['pickup'] }}</pickup>
                    <delivery>{{ $product['delivery'] }}</delivery>
                    <categoryId>{{ $product['category'] }}</categoryId>
                </offer>
            @endforeach
        </offers>
    </shop>
</yml_catalog>
