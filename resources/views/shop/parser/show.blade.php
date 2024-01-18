@extends('layouts.shop')

@section('body')
    page
@endsection

@section('main')
    container-xl
@endsection

@section('content')
    <h1>Заказ товаров с каталога IKEA.PL</h1>
    <section class="parser" id="parser-container">
    <div class="left-side" id="left-side">
        <div id="parser-search">
            <div class="parser-card-search">
                <div class="parser-card-search--header">
                    <p>Рассчитайте стоимость любого товара из каталога Икеа самостоятельно и Вы сразу узнаете стоимость заказа.</p>
                    <p><b>Для точного расчёта данный инструмент использовать без VPN</b></p>
                    <h3 id="parser-condition" class="_name_">Найти товар</h3>
                </div>

                <div class="parser-card-search--find">
                    <div id="parser-condition-text" class="parser-card-search--text">
                        Скопируйте и вставьте в поле номер артикула товара или ссылку с сайта <a href="https://IKEA.PL" target="_blank">IKEA.PL</a>
                    </div>
                    <div class="parser-card-search--form">
                        <input id="search-field" type="text" name="search-data" class="form-control"/>
                        <button id="search-button" onclick="ym(88113821,'reachGoal','target-parser'); return true;">Искать</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="parser-list"></div>
    </div>
    <div class="right-side" id="right-side">
        <div id="parser-amount">
            <div class="parser-card-amount">
                <h3>Стоимость заказа</h3>
                <table class="parser-amount-table" style="width: 100%">
                    <tr>
                        <td class="parser-amount-table-caption">Доставка до Калининграда (<span id="weight">{weight}</span> кг)</td>
                        <td class="parser-amount-table-value">
                            <span id="delivery">{delivery}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="parser-amount-table-caption">Стоимость товаров:</td>
                        <td class="parser-amount-table-value">
                            <span id="amount">{amount}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td class="parser-amount-table-caption">Итого к оплате:</td>
                        <td class="parser-amount-table-value">
                            <span id="full-amount">{full-amount}</span>
                        </td>
                    </tr>
                </table>

                <div class="parser-card-amount--button">
                    <button id="amount-button" onclick="ym(88113821,'reachGoal','parser-prepare'); return true;">Оформить заказ</button>
                </div>
            </div>

        </div>
        <div class="parser-info"></div>
    </div>
    </section>
@endsection
