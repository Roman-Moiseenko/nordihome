import jQuery from "jquery";
import common from "@/_common.js";

window.$ = jQuery;

(function () {
    "use strict";
    //Устанавливаем в сессию таймзону клиента
    sessionStorage.setItem("time", -(new Date().getTimezoneOffset()));

    const loginPopup = $('#login-popup');
    if (loginPopup.length) {
        const form = $('form#login-form');
        const buttonLogin = $('#button-login');
        const inputEmail = loginPopup.find('input[name="email"]');
        const inputPassword = loginPopup.find('input[name="password"]');
        const inputVerify = loginPopup.find('input[name="verify_token"]');
        const checkAgreement = loginPopup.find('input[name="agreement"]');
        const tokenError = $('#token-error');
        const passwordError =$('#password-error')

        inputVerify.parent().hide();
        buttonLogin.on('click', function () {
            if (inputEmail.val().length === 0 || inputPassword.val().length === 0 || !common.isEmail(inputEmail.val())) {
                form.addClass('was-validated');
                return true;
            }
            if (inputVerify.parent().is(':visible') && inputVerify.val().length === 0) {
                form.addClass('was-validated');
                return true;
            }
            if (checkAgreement.prop('required') && !checkAgreement.is(':checked')) {
                form.addClass('was-validated');
                return true;
            }
            $.post('/login-client',
                {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    email: inputEmail.val(),
                    password: inputPassword.val(),
                    verify_token: inputVerify.val(),
                    agreement: checkAgreement.prop('checked') ? 1 : 0
                }, function (data) {
                    console.log(data)

                    tokenError.hide();
                    passwordError.hide();
                    if (data === "token") tokenError.show(); //неверный токен
                    if (data === "verification") {
                        inputEmail.prop('disabled', true);
                        inputPassword.prop('disabled', true);
                        inputVerify.prop('required', true);
                        checkAgreement.prop('required', true);
                        inputVerify.parent().show();
                    }
                    if (data === "password") passwordError.show(); //Неверный пароль
                    if (data === "login") location.reload(); //Аутентификация прошла
                    if (data === "banned") {
                        alert('Ваш аккаунт заблокирован!')
                    }
                }
            );

        });
    }

})();
