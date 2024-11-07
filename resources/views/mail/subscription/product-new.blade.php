<x-mail::message>
    # Поступление нового товара

    @component('mail::table')
        | Товар | Цена | Ссылка |
        |:------|:----:|-------:|
        @foreach($products as $product)
            | {{ $product->name}} | {{ price($product->getPrice()) }} | <a
                    href="{{ route('shop.product.view', $product->slug) }}"
                    target="_blank">{{ route('shop.product.view', $product->slug) }}</a> |
        @endforeach
    @endcomponent

    С уважением,<br>
    {{ config('app.name') }}
</x-mail::message>
