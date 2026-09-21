<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<yml_catalog date="{{ $date }}">
    <shop>
        <name>{{ $data->info->name }}</name>
        <company>{{ $data->info->company }}</company>
        <url>{{ $data->info->url }}</url>
        <platform>CRM Web39</platform>
        <version>0.9.09</version>
        <currencies>
            <currency id="RUR" rate="1"/>
        </currencies>
        <categories>
            @foreach($data->categories as $category)
                @if($category->parent)
                    <category id="{{ $category->id }}" parentId="{{ $category->parent }}">{{ $category->name }}</category>
                @else
                    <category id="{{ $category->id }}">{{ $category->name }}</category>
                @endif
            @endforeach
        </categories>
        <offers>
            @foreach($data->products as $product)
                <offer id="{{ $product->id }}" available="true">
                    <name>{{ $product->name }}</name>
                    <description>
                        <![CDATA[ {!! $product->description !!} ]]>
                    </description>
                    @foreach($product->images as $image)
                    <picture>{{ $image }}</picture>
                    @endforeach
                    <url>{{ $product->url }}</url>
                    <price>{{ $product->price }}</price>
                    @if($data->info->preprice && $product->preprice != 0) <oldprice>{{ $product->preprice }}</oldprice> @endif
                    <currencyId>RUR</currencyId>
                    <vendorCode>{{ $product->code }}</vendorCode>
                    <store>{{ $product->store ? 'true' : 'false' }}</store>
                    <pickup>{{ $product->pickup ? 'true' : 'false' }}</pickup>
                    <delivery>{{ $product->delivery ? 'true' : 'false' }}</delivery>
                    <categoryId>{{ $product->category }}</categoryId>
                </offer>
            @endforeach
        </offers>
    </shop>
</yml_catalog>
