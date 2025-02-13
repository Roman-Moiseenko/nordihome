<div class="modal fade" id="login-popup" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="login-form" class="p-3 needs-validation" action="{{ route('login') }}" method="post" role="form" novalidate>
                @csrf
                <input type="hidden" name="intended">
                <div class="d-flex justify-content-between p-2 text-center mb-4 align-items-center">
                    <p class="modal-title fs-4" id="exampleModalLabel">Войти или создать профиль</p>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Электронная почта"
                                   required autofocus="autofocus" autocomplete="off">
                            <label for="email">Электронная почта</label>
                        </div>
                        <div class="form-floating input-group ">
                            <input type="password" class="form-control" name="password" id="password" placeholder="Пароль"
                                   minlength="6" required autocomplete="on" aria-describedby="show-hide-password">
                            <label for="password" style="z-index: 9999 !important;">Пароль</label>
                            <button id="show-hide-password" class="btn btn-secondary" type="button" data-target-input="#password"><i class="fa-light fa-eye"></i></button>
                        </div>
                        <div id="password-error" class="fs-7 text-danger" style="display: none">Неверный пароль</div>
                        <div class="form-floating my-3">
                            <input type="text" class="form-control" name="verify_token" id="verify_token"
                                   placeholder="Код верификации" autocomplete="on" autocomplete="off">
                            <label for="verify_token">Код подтверждения (с почты)</label>
                            <span id="token-error" class="fs-7 text-danger" style="display: none">Неверный код подтверждения</span>
                        </div>
                        <div class="fs-7 mt-3">
                            <a href="{{ route('password.request') }}">Забыли пароль?</a>
                        </div>
                        <h5 class="mt-3" style="color: var(--bs-secondary-700);">В текущий момент регистрация не доступна.</h5>
                        <h5>Заказы оформляются через форму заказа из Корзины</h5>
                        <div class="d-flex justify-content-center my-5">
                            <button id="button-login" type="button" class="btn-nb" disabled>Отправить</button>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <svg width="59px" height="28px" viewBox="0 0 59 28"><title>logo</title><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-248.000000, -62.000000)" fill="#CF0A2C"><g transform="translate(0.000000, 36.000000)"><g transform="translate(248.000000, 26.000000)"><path d="M29.9145594,9.88941849 L32.6463591,5.19161106 L41.9236592,4.60438513 L42.2482294,4.03050524 L33.6741649,3.44327931 L35.6892053,0.0133460439 L51.5931483,0.0133460439 C56.4887498,0.0133460439 60.099594,1.74833174 58.6931228,6.80648236 C58.2738862,8.35462345 56.2858934,12.2249762 50.4436286,13.8398475 C51.6878146,13.986654 54.7712321,15.3346044 54.2302817,18.8312679 C53.2836184,25.0371783 45.1558381,27.986654 40.6253781,27.986654 L21.8408741,28 L20.9077345,24.4099142 L30.7530326,23.729266 L31.0911266,23.1553861 L20.4073554,22.514776 L19.2578357,18.257388 L34.4450193,17.3765491 L34.7831134,16.8160153 L7.50568737,15.1344137 L8.81749219,12.8922784 L38.1911011,11.0104862 L38.5291952,10.4499523 L29.9145594,9.88941849 M42.2076582,10.8236416 L44.9124104,10.8102955 C46.7245943,10.7969495 48.6043971,9.9828408 49.2941089,8.34127741 C49.9297257,6.80648236 49.0777288,5.49857007 47.9417328,5.51191611 L45.3181232,5.51191611 L42.2076582,10.8236416 Z M39.0836694,16.1487131 L35.824443,21.7407054 L38.934908,21.7407054 C40.3819504,21.7407054 42.9649888,21.0333651 43.7628907,19.0047664 C44.5066975,17.1096282 43.1407977,16.1487131 42.1941344,16.1487131 L39.0836694,16.1487131 L39.0836694,16.1487131 Z M15.39004,24.7836034 L13.4967135,27.9733079 L0,28 L1.31180482,25.7578646 L15.39004,24.7836034 L15.39004,24.7836034 Z M16.3367033,0 L27.1962834,0.0133460439 L28.0212328,3.042898 L15.051946,2.24213537 L16.3367033,0 Z M28.6298021,5.44518589 L29.8198931,9.88941849 L11.2788167,8.68827455 L12.5770978,6.45948522 L28.6298021,5.44518589 Z M19.1902169,18.257388 L16.8370825,22.2878932 L3.75960556,21.5538608 L5.07141038,19.3117255 L19.1902169,18.257388 L19.1902169,18.257388 Z"></path></g></g></g></g></svg>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
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
