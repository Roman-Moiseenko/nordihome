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
                            <input type="email" class="form-control" name="email" placeholder="Электронная почта" required autofocus="autofocus">
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
                            <input type="text" class="form-control" name="verify_token" id="verify_token" placeholder="Код верификации" autocomplete="on">
                            <label for="verify_token">Код подтверждения (с почты)</label>
                            <span id="token-error" class="fs-7 text-danger" style="display: none">Неверный код подтверждения</span>
                        </div>
                        <div class="d-flex justify-content-center my-5">
                            <button id="button-login" type="button" class="btn btn-dark fs-5 py-2 px-3">Отправить</button>
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
    document.addEventListener("DOMContentLoaded", function() {
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
    });
</script>
