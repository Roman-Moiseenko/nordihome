<div>

    <div class="title-page">
        <h1>@if(!empty($items))Моя корзина @else Ваша корзина пуста @endif</h1>
    </div>

    <div class="screen-action">
        <div class="left-list-block">
            <div class="box-card d-flex panel-cart-manage align-items-center">
                <div class="checkbox-group">
                    <input class="" type="checkbox" value="" id="checked-all"
                           wire:change="check_items" wire:model="check_all" wire:loading.attr="disabled"
                    >
                    <label class="" for="checked-all">Выбрать все</label>
                </div>
                @if($button_trash)
                    <button id="cart-trash" class="btn btn-light ms-3 p-1" wire:click="del_select">Удалить выбранные</button>
                @endif
            </div>
            @foreach($items as $item)
                <livewire:n-b-russia.cart.item :item="$item" :key="$item['product_id']" :user="$user"/>
            @endforeach
        </div>

        @if(!empty($items))
            <div class="right-action-block">
                <div class="sticky-block">
                    <div>
                        <form id="to-order" method="POST" action="{{ route('shop.order.create') }}">
                            @csrf
                            <input type="hidden" name="preorder" value="false">
                            <!--  data-bs-toggle="modal" data-bs-target="#login-popup" type="button" -->
                            <button id="button-to-order" class="btn-cart w-100 py-3"
                                    @guest()
                                        data-bs-toggle="modal" data-bs-target="#new-order" type="button"

                                    @endguest
                                    @auth('user')
                                        type="submit"
                                @endauth
                            >Перейти к оформлению
                            </button>
                        </form>
                        <div class="full-cart-order--info">
                            <div class="d-flex justify-content-between">
                                <div class="fs-5">Товаров в корзине</div>
                                <div id="cart-count-products" class="fs-5">{{ $count }}</div>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <div class="fs-6">Полная стоимость корзины</div>
                                <div id="cart-full-amount" class="fs-6">{{ price($amount) }}</div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="fs-7">Ваша скидка</div>
                                <div id="cart-full-discount" class="fs-7">{{ price($discount) }}</div>
                            </div>
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <div class="fs-5">Сумма к оплате</div>
                                <div id="cart-amount-pay" class="fs-5">{{ price($amount - $discount) }}</div>
                            </div>
                        </div>
                    </div>

                    <div id="cart-preorder" class="mt-3" @if(!$preorder) style="display: none" @endif>
                        <div class="fs-6">В корзине имеется товар, которого нет в наличии.</div>
                        <div class="fs-7 mt-1">Вы можете выбрать убрать товар которого нет на складе, и заказать только по наличию.<br>
                            Либо, сделать предзаказ на товар которого нет в наличии.
                        </div>
                        <div class="checkbox-group mt-2">
                            <input id="preorder-false" type="radio" class="form-check-inline"
                                   data-state="change" autocomplete="off"
                                   name="pre-order"
                            >
                            <label for="preorder-false">Отгрузить по наличию на складе</label>
                        </div>
                        <div class="checkbox-group">
                            <input id="preorder-true" type="radio" class="form-check-inline" data-state="change" autocomplete="off" name="pre-order" checked="checked">
                            <label for="preorder-true">Оформить с предзаказом</label>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
