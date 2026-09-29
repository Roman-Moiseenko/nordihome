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
        const checkNewsletter = loginPopup.find('input[name="newsletter"]');
        const tokenError = $('#token-error');
        const passwordError =$('#password-error')
        const forgotPassword = $('#forgot-password');
        const registration = $('#registration');
        const repeatPassword = $('#repeat-password');
        const inputPasswordConfirmation = loginPopup.find('input[name="password_confirmation"]');
        const passwordConfirmationError = $('#password-confirmation-error');
        const showHidePasswordConfirmation = $('#show-hide-password-confirmation');
        let isRegistration = false;

        inputVerify.parent().hide();

        //Переключение режима Войти/Регистрация
        registration.on('click', function (e) {
            e.preventDefault();
            isRegistration = !isRegistration;
            if (isRegistration) {
                buttonLogin.text('Зарегистрироваться');
                repeatPassword.show();
                inputPasswordConfirmation.prop('required', true);
                registration.find('a').text('Войти');
                forgotPassword.hide();
            } else {
                buttonLogin.text('Войти');
                repeatPassword.hide();
                inputPasswordConfirmation.prop('required', false).val('');
                passwordConfirmationError.hide();
                registration.find('a').text('Регистрация');
                forgotPassword.show();
            }
        });

        //Показать/скрыть пароль в поле повтора пароля
        if (showHidePasswordConfirmation.length) {
            showHidePasswordConfirmation.on('click', function () {
                const input = $($(this).data('target-input'));
                input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
            });
        }
        buttonLogin.on('click', function () {
            if (inputEmail.val().length === 0 || inputPassword.val().length === 0 || !common.isEmail(inputEmail.val())) {
                form.addClass('was-validated');
                return true;
            }
            if (repeatPassword.is(':visible')) {
                if (inputPasswordConfirmation.val().length === 0) {
                    form.addClass('was-validated');
                    return true;
                }
                if (inputPasswordConfirmation.val() !== inputPassword.val()) {
                    passwordConfirmationError.show();
                    return true;
                }
                passwordConfirmationError.hide();
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
                    agreement: checkAgreement.prop('checked') ? 1 : 0,
                    newsletter: checkNewsletter.prop('checked') ? 1 : 0
                }, function (data) {
                    console.log(data)

                    tokenError.hide();
                    passwordError.hide();
                    if (data === "token") tokenError.show(); //неверный токен
                    if (data === "verification") {
                        inputEmail.prop('disabled', true);
                        inputPassword.prop('disabled', true);
                        inputPasswordConfirmation.prop('disabled', true);
                        inputVerify.prop('required', true);
                        checkAgreement.prop('required', true);
                        inputVerify.parent().show();
                        forgotPassword.hide();
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
