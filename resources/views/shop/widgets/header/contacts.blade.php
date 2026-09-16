<div class="col-auto mob-h-phone m-l_10">
    @if(isset($contacts['phone']))
        <a href="{{ $contacts['phone']->url }}" class="f-z_16"
           data-analytics-action="contact_click"
           data-analytics-payload='{"channel":"{{ $contacts['phone']->channel }}","placement":"header"}'
        ><b>{{ phone($contacts['phone']->url) }}</b></a>
    @endif
    <br><span class="f-z_13">по России бесплатно</span>
</div>
<div class="d-flex ms-2 mob-h-social">
@if(isset($contacts['phone']))
    <a href="{{ $contacts['phone']->url }}" target="_blank" class="m-r_5"
       data-analytics-action="contact_click"
       data-analytics-payload='{"channel":"{{ $contacts['phone']->channel }}","placement":"header"}'
    >{!! $contacts['phone']->svg !!}</a>
@endif
@if(isset($contacts['telegram']))
    <a href="{{ $contacts['telegram']->url }}" target="_blank" class="m-r_5"
       data-analytics-action="contact_click"
       data-analytics-payload='{"channel":"{{ $contacts['telegram']->channel }}","placement":"header"}'
    >{!! $contacts['telegram']->svg !!}</a>
@endif
@if(isset($contacts['max_bot_1']))
    <a href="{{ $contacts['max_bot_1']->url }}" target="_blank" class="m-r_5"
       data-analytics-action="contact_click"
       data-analytics-payload='{"channel":"{{ $contacts['max_bot_1']->channel }}","placement":"header"}'
    >{!! $contacts['max_bot_1']->svg !!}</a>
@endif
@if(isset($contacts['vk']))
    <a href="{{ $contacts['vk']->url }}" target="_blank"
       data-analytics-action="contact_click"
       data-analytics-payload='{"channel":"{{ $contacts['vk']->channel }}","placement":"header"}'
    >{!! $contacts['vk']->svg !!}</a>
@endif
</div>

