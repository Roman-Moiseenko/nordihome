(function () {
    "use strict";
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('.create-add-product')
        .each(function () {
            let buttonCreate = $(this).find('#create-product');
            let inputName = $(this).find('input[name="name"]');
            let inputCode = $(this).find('input[name="code"]');
            let inputCategory = $(this).find('select[name="category_id"]');
            let inputBrand = $(this).find('select[name="brand_id"]');
            let inputPrice = $(this).find('input[name="price"]');
            let route = $(this).find("#data").data('route');

            const myModal = tailwind.Modal.getInstance(document.querySelector("#modal-create-order"));
            buttonCreate.on('click', function () {
                let routeAdd = buttonCreate.data('route');
                let eventAdd = buttonCreate.data('event');
                let quantity = 1;

                $.post(route,
                    {
                        name: inputName.val(),
                        code: inputCode.val(),
                        category_id: inputCategory.val(),
                        brand_id: inputBrand.val(),
                        price: inputPrice.val(),
                    },//ajax запрос
                    function (data) {

                        let product_id = data;
                        if (routeAdd !== '') send_route(routeAdd, quantity, product_id);
                        if (eventAdd !== '') send_event(eventAdd, quantity, product_id);
                        inputName.val('')
                        inputCode.val('')
                        inputCategory.val(null)
                        inputBrand.val(null)
                        inputPrice.val(0)
                        myModal.hide();
                    });
            });

            function send_route(routeAdd, quantity, selectProductId) {
                let _params = '_token=' + token + '&product_id=' + selectProductId + '&quantity=' + quantity + '&preorder=1';
                let request = new XMLHttpRequest();
                request.open('POST', routeAdd);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(_params);
                request.onreadystatechange = function () {
                    if (this.readyState === 4 && this.status === 200) {
                        if (request.responseURL !== '') window.location.href = request.responseURL;
                    }
                };
            }

            function send_event(eventAdd, quantity, selectProductId) {
                window.Livewire.dispatch(
                    eventAdd,
                    {
                        product_id: selectProductId,
                        quantity: quantity,
                        preorder: true,
                    }
                );
            }
        });
})();
