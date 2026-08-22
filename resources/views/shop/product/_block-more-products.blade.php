<div class="block-more-product">
    <div class="container"><div class="f-z_23 m-b_10 f-w_600 t-t_uppercase">НЕ СМОГЛИ НАЙТИ ТО, ЧТО ХОТЕЛИ?</div>
        <div>
            <p>Есть два решения:</p>
            <ol>
                <li class="m-b_10">Наши менеджеры помогут Вам с подбором. Для этого Вам просто надо связаться с нами по телефону:
                    @if(isset($contacts['phone_1']))
                    <a href="{{ $contacts['phone_1']->url }}" class="t-color_orange">{{ phone( $contacts['phone_1']->url ) }}</a>
                    @endif
                    <br>либо в мессенджерах
                    @if(isset($contacts['max_bot']))
                        <a href="{{ $contacts['max_bot']->url }}" target="_blank">{!! $contacts['max_bot']->svg !!}</a>
                    @endif
                    @if(isset($contacts['telegram_bot']))
                    <a href="{{ $contacts['telegram_bot']->url }}" target="_blank">{!! $contacts['telegram_bot']->svg !!}</a></li>
                @endif
                <li>Вы можете сделать заказ из полного ассортимента IKEA.pl, а мы доставим выбранные позиции в кратчайшие сроки, не зависимо от веса и объёма.</li>
            </ol>
        </div>
        <a href="/ikea" class="btn btn-white t-t_uppercase f-z_14 m-t_20">Заказать</a>
    </div>
</div>
