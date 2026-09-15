<div class="block-more-product">
    <div class="container"><div class="f-z_23 m-b_10 f-w_600 t-t_uppercase">НЕ СМОГЛИ НАЙТИ ТО, ЧТО ХОТЕЛИ?</div>
        <div>
            <p>Есть два решения:</p>
            <ol>
                <li class="m-b_10">Наши менеджеры помогут Вам с подбором. Для этого Вам просто надо связаться с нами по телефону:
                    @if(isset($contacts['phone_1']))
                    <a href="{{ $contacts['phone_1']->url }}" class="t-color_orange"
                       data-analytics-action="contact_click"
                       data-analytics-payload='{"channel":"{{ $contacts['phone_1']->channel }}","placement":"page_product"}'
                    >{{ phone( $contacts['phone_1']->url ) }}</a>
                    @endif
                    <br>либо в мессенджерах
                    @if(isset($contacts['max_bot']))
                        <a href="{{ $contacts['max_bot']->url }}" target="_blank"
                           data-analytics-action="contact_click"
                           data-analytics-payload='{"channel":"{{ $contacts['max_bot']->channel }}","placement":"page_product"}'
                        >{!! $contacts['max_bot']->svg !!}</a>
                    @endif
                    @if(isset($contacts['telegram_bot']))
                    <a href="{{ $contacts['telegram_bot']->url }}" target="_blank"
                       data-analytics-action="contact_click"
                       data-analytics-payload='{"channel":"{{ $contacts['telegram_bot']->channel }}","placement":"page_product"}'
                    >{!! $contacts['telegram_bot']->svg !!}</a></li>
                @endif
                <li>Вы можете сделать заказ из полного ассортимента IKEA.pl, а мы доставим выбранные позиции в кратчайшие сроки, не зависимо от веса и объёма.</li>
            </ol>
        </div>
        <a href="/ikea" class="btn btn-white t-t_uppercase f-z_14 m-t_20">Заказать</a>
    </div>
</div>
