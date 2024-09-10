import jQuery from 'jquery';
window.$ = jQuery;
/*
window.addEventListener("DOMContentLoaded", function() {
    [].forEach.call( document.querySelectorAll('.mask-phone'), function(input) {
        let keyCode;
        function mask(event) {
            event.keyCode && (keyCode = event.keyCode);
            let pos = this.selectionStart;
            if (pos < 3) event.preventDefault();
            let matrix = "8 (___) ___-__-__",
                i = 0,
                def = matrix.replace(/\D/g, ""),
                val = this.value.replace(/\D/g, ""),
                new_value = matrix.replace(/[_\d]/g, function(a) {
                    return i < val.length ? val.charAt(i++) : a
                });
            i = new_value.indexOf("_");
            if (i !== -1) {
                i < 5 && (i = 3);
                new_value = new_value.slice(0, i)
            }
            let reg = matrix.substr(0, this.value.length).replace(/_+/g,
                function(a) {
                    return "\\d{1," + a.length + "}"
                }).replace(/[+()]/g, "\\$&");
            reg = new RegExp("^" + reg + "$");
            if (!reg.test(this.value) || this.value.length < 5 || keyCode > 47 && keyCode < 58) {
                this.value = new_value;
            }
            if (event.type === "blur" && this.value.length < 5) {
                this.value = "";
            }
        }

        input.addEventListener("input", mask, false);
        input.addEventListener("focus", mask, false);
        input.addEventListener("blur", mask, false);
        input.addEventListener("keydown", mask, false);
    });
});
*/

const mask_phone = (input) => {
    let keyCode;

    function mask(event) {

        event.keyCode && (keyCode = event.keyCode);
        //console.log(event.keyCode);
        let pos = this.selectionStart;
        //console.log('start pos', pos);
        if (pos < 2 && event.keyCode !== 8 && event.keyCode !== 46) { // Allow backspace (8) and delete (46) keys
            event.preventDefault();
            //console.log('pos', pos);
        }
        //console.log('this.value', this.value);
        let matrix = "8 (___) ___-__-__",
            i = 0,
            def = matrix.replace(/\D/g, ""),
            val = this.value.replace(/\D/g, ""),
            new_value = matrix.replace(/[_\d]/g, function(a) {
                //console.log('a', a);
                //console.log('i<', i, val.length);
                return i < val.length ? val.charAt(i++) : a;
            });
        //console.log('i', i);
        //console.log('val', val);
        //console.log('new_value', new_value);
        i = new_value.indexOf("_");
        //console.log('i=', i);
        if (i !== -1) {
            i < 4 && (i = 2);
            new_value = new_value.slice(0, i);
            //console.log('2 new_value', new_value);
        }

        let reg = matrix.substr(0, this.value.length).replace(/_+/g, function(a) {
            return "\\d{1," + a.length + "}";
        }).replace(/[+()]/g, "\\$&");
        //console.log('reg', reg);
        reg = new RegExp("^" + reg + "$");
        //console.log('reg2', reg);
        if (!reg.test(this.value) || this.value.length < 4 || (keyCode > 47 && keyCode < 58)) {
            this.value = new_value;
            //console.log('3 new_value', new_value);
        }

        if (event.type === "blur" && this.value.length < 4) {
            this.value = "";
        }
    }

    function handleDelete(event) {
        if (this.selectionStart === 0 && this.selectionEnd === this.value.length) {
            this.value = "";
        }
    }

    function handleInput(event) {
        if (this.value === "") {
            this.value = "8 ";
        }
    }

    input.addEventListener("input", mask, false);
    input.addEventListener("focus", mask, false);
    input.addEventListener("blur", mask, false);
    input.addEventListener("keydown", mask, false);
    input.addEventListener("keydown", handleDelete, false);
    input.addEventListener("input", handleInput, false);
}

window.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.mask-phone').forEach(mask_phone
        /*
        function(input) {
        let keyCode;

        function mask(event) {

            event.keyCode && (keyCode = event.keyCode);
            //console.log(event.keyCode);
            let pos = this.selectionStart;
            //console.log('start pos', pos);
            if (pos < 2 && event.keyCode !== 8 && event.keyCode !== 46) { // Allow backspace (8) and delete (46) keys
                event.preventDefault();
                //console.log('pos', pos);
            }
            //console.log('this.value', this.value);
            let matrix = "8 (___) ___-__-__",
                i = 0,
                def = matrix.replace(/\D/g, ""),
                val = this.value.replace(/\D/g, ""),
                new_value = matrix.replace(/[_\d]/g, function(a) {
                    //console.log('a', a);
                    //console.log('i<', i, val.length);
                    return i < val.length ? val.charAt(i++) : a;
                });
            //console.log('i', i);
            //console.log('val', val);
            //console.log('new_value', new_value);
            i = new_value.indexOf("_");
            //console.log('i=', i);
            if (i !== -1) {
                i < 4 && (i = 2);
                new_value = new_value.slice(0, i);
                //console.log('2 new_value', new_value);
            }

            let reg = matrix.substr(0, this.value.length).replace(/_+/g, function(a) {
                return "\\d{1," + a.length + "}";
            }).replace(/[+()]/g, "\\$&");
            //console.log('reg', reg);
            reg = new RegExp("^" + reg + "$");
            //console.log('reg2', reg);
            if (!reg.test(this.value) || this.value.length < 4 || (keyCode > 47 && keyCode < 58)) {
                this.value = new_value;
                //console.log('3 new_value', new_value);
            }

            if (event.type === "blur" && this.value.length < 4) {
                this.value = "";
            }
        }

        function handleDelete(event) {
            if (this.selectionStart === 0 && this.selectionEnd === this.value.length) {
                this.value = "";
            }
        }

        function handleInput(event) {
            if (this.value === "") {
                this.value = "8 ";
            }
        }

        input.addEventListener("input", mask, false);
        input.addEventListener("focus", mask, false);
        input.addEventListener("blur", mask, false);
        input.addEventListener("keydown", mask, false);
        input.addEventListener("keydown", handleDelete, false);
        input.addEventListener("input", handleInput, false);

    }*/

    );


    document.querySelectorAll('.mask-email').forEach(function(input) {
        input.addEventListener("blur", function () {
            let check = /^.+@[a-zA-Z0-9_-]+\.[a-z]+$/i.test(input.value);
            if (!check && input.value !== '') {
                window.Livewire.dispatch('window-notify', {title: 'Ошибка', message: 'Неверный формат почты'});
                this.focus();
            }

        });
    });
});
