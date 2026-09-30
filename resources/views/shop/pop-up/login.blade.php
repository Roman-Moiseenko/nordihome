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
                        <div class="form-floating input-group mt-3" id="repeat-password" style="display: none">
                            <input type="password" class="form-control" name="password_confirmation" id="password-confirmation"
                                   placeholder="Повторите пароль" minlength="6" autocomplete="off">
                            <label for="password-confirmation" style="z-index: 9999 !important;">Повторите пароль</label>
                            <button id="show-hide-password-confirmation" class="btn btn-secondary" type="button" data-target-input="#password-confirmation"><i class="fa-light fa-eye"></i></button>
                        </div>
                        <div id="password-confirmation-error" class="fs-7 text-danger mt-2" style="display: none">Пароли не совпадают</div>
                        <div class="form-floating my-3">
                            <input type="text" class="form-control" name="verify_token" id="verify_token"
                                   placeholder="Код верификации" autocomplete="off">
                            <label for="verify_token">Код подтверждения (с почты)</label>
                            <div class="form-check checked mt-2 p-0">
                                <input class="form-check-input" type="checkbox" name="agreement" id="agreement"
                                       value="Согласие на обработку персональных данных">
                                <label class="form-check-label f-z_14" for="agreement">Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
                                </label>
                                <div class="invalid-feedback">
                                    Необходимо согласие на обработку персональных данных
                                </div>
                            </div>
                            <div class="form-check mt-2 p-0">
                                <input class="form-check-input" type="checkbox" name="newsletter" id="newsletter" value="1">
                                <label class="form-check-label f-z_14" for="newsletter">Согласен получать новости, акции и специальные предложения по электронной почте</label>
                            </div>
                            <span id="token-error" class="fs-7 text-danger" style="display: none">Неверный код подтверждения</span>
                        </div>
                        <div class="d-flex" style="justify-content: space-between">
                            <div class="fs-7 mt-3" id="forgot-password">
                                <a href="{{ route('password.request') }}">Забыли пароль?</a>
                            </div>
                            <div class="fs-7 mt-3" id="registration">
                                <a href="#">Регистрация</a>
                            </div>
                        </div>



                        <div class="d-flex justify-content-center my-5">
                            <button id="button-login" type="button" class="btn btn-dark fs-5 py-2 px-3">Войти</button>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <img src="/images/icons/nordi-home-rus.svg" alt="Логотип Норди Хоум" class="img-fluid img-logo">
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
