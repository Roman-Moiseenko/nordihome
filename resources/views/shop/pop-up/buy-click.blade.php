
<div class="modal fade" id="buy-click" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="buy-click-form" class="p-3 needs-validation"  method="get" role="form" novalidate>
                @csrf
                <input type="hidden" name="intended">
                <input id="one-click-product-id" type="hidden" name="product_id">
                <div class="d-flex justify-content-between p-2 text-center mb-4 align-items-center">
                    <p class="modal-title fs-4" id="exampleModalLabel">Быстрая покупка</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control mask-email" name="email" placeholder="Электронная почта" required
                                   value="{{ is_null($user) ? '' : $user->email }}" autocomplete="off">
                            <label for="email">Электронная почта</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control mask-phone" name="phone" placeholder="Телефон" required
                                   value="{{ is_null($user) ? '' : $user->phone }}" autocomplete="off">
                            <label for="phone">Телефон</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" name="payment" id="payment">
                                <option value="0" selected></option>
                                @foreach(App\Modules\Order\Entity\Payment\PaymentHelper::payments() as $payment)
                                    <option value="{{ $payment['class'] }}">{{ $payment['name'] }}</option>
                                @endforeach
                            </select>
                            <label for="payment">Выберите способ оплаты товара</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" name="delivery" id="delivery">
                                <option value="0" selected></option>
                                @foreach(\App\Modules\Accounting\Entity\Storage::where('point_of_delivery', true)->get() as $storage)
                                    <option value="{{ $storage->id }}">Самовывоз: {{ $storage->address }}</option>
                                @endforeach
                                <option value="local">Доставка по Калининградской области</option>
                                <option value="region">Отправка транспортной компанией по России</option>
                            </select>
                            <label for="delivery">Выберите способ получения товара</label>
                        </div>
                        <div id="delivery_address" class="form-floating mb-3" style="display: none">
                            <input type="text" class="form-control" name="address" placeholder="Адрес" autocomplete="off">
                            <label for="address">Адрес</label>
                        </div>
                        <div id="buy-click-error" class="fs-7 text-danger"></div>
                        <div class="d-flex justify-content-center my-5">
                            <button id="button-buy-click" type="button" class="btn btn-dark fs-5 py-2 px-3 e-buy-click" data-product="">Оформить</button>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <img src="/images/logo-nordi-home-2.svg" alt="NORDI Home" class="img-fluid img-logo">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>

    let selectDelivery = document.getElementById('delivery');
    let blockAddress = document.getElementById('delivery_address');
    selectDelivery.addEventListener('change', function () {
        let _value = selectDelivery.value;
        if (_value === 'local' || _value === 'region') {
            blockAddress.style.display = 'block';
        } else {
            blockAddress.style.display = 'none';
        }
    });
/*    document.addEventListener("DOMContentLoaded", function() {
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });*/
</script>
