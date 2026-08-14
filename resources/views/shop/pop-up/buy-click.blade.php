<div class="modal fade" id="buy-click" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="buy-click-form" class="p-3 needs-validation" method="get" role="form" novalidate>
                @csrf
                <input type="hidden" name="intended">
                <input id="one-click-product-id" type="hidden" name="productId">
                <div class="d-flex justify-content-between p-2 text-center mb-4 align-items-center">
                    <p class="modal-title fs-4" id="exampleModalLabel">Быстрая покупка</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control mask-email" name="email"
                                   placeholder="Электронная почта" required
                                   value="{{ is_null($client) ? '' : $client->email }}" autocomplete="off">
                            <label for="email">Электронная почта</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control mask-phone" name="phone" placeholder="Телефон"
                                   required
                                   value="{{ is_null($client) ? '' : $client->phone }}" autocomplete="off">
                            <label for="phone">Телефон</label>
                        </div>

                        <div class="form-floating mb-3">
                            <select class="form-select" name="isPickup" id="delivery">
                                <option value="0" selected></option>
                                @foreach(\App\Modules\Accounting\Entity\Storage::where('point_of_delivery', true)->get() as $storage)
                                    <option value="1">Самовывоз: {{ $storage->address }}</option>
                                @endforeach
                                <option value="0">Доставка</option>
                            </select>
                            <label for="delivery">Выберите способ получения товара</label>
                        </div>
                        <div id="delivery_address" class=" mb-3" style="display: none">
                            <div class="form-floating input-group mb-1">
                                <select class="form-select" name="region" id="input-region">
                                    <option></option>
                                </select>
                                <label for="region">Регион</label>
                                <input type="hidden" class="form-control" name="regionCode" id="input-region-code"
                                       value="" readonly style="max-width:100px;"
                                       placeholder="Код">
                            </div>
                            <div class="form-floating">
                                <input type="text" class="form-control" name="address" placeholder="Адрес"
                                       autocomplete="off">
                                <label for="address">Адрес</label>
                            </div>
                        </div>
                        <div class="form-check checked mt-2 p-0">
                            <input class="form-check-input" type="checkbox" name="agreement" id="agreement"
                                   value="Согласие на обработку персональных данных">
                            <label class="form-check-label f-z_14" for="agreement">Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
                            </label>
                            <div class="invalid-feedback">
                                Необходимо согласие на обработку персональных данных
                            </div>
                        </div>

                        <div id="buy-click-error" class="fs-7 text-danger"></div>
                        <div class="d-flex justify-content-center my-5">
                            <button id="button-buy-click" type="button" class="btn btn-dark fs-5 py-2 px-3 e-buy-click"
                                    data-product="">Оформить
                            </button>
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
        if (_value === '0') {
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
