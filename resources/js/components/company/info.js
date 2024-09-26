(function () {
    "use strict";

    let titleModal = $('#title-modal');
    let formModal = $('#modal-contact');
    let inputPhone = $('#input-phone');
    let inputEmail =$('#input-email');
    let inputPost =$('#input-post');
    let inputSurname =$('#input-fullname\\[surname\\]');
    let inputFirstname =$('#input-fullname\\[firstname\\]');
    let inputSecondname =$('#input-fullname\\[secondname\\]');



    $('.edit-modal-contact').each(function () {
        let button = $(this);
        button.on('click', function () {
            let contact = button.data('contact');
            console.log(contact);
            titleModal.html('Редактировать')
            formModal.attr('action', button.data('route'))
            inputPhone.val(contact.phone);
            inputEmail.val(contact.email);
            inputPost.val(contact.post);
            inputSurname.val(contact.fullname.surname);
            inputFirstname.val(contact.fullname.firstname);
            inputSecondname.val(contact.fullname.secondname);

        });
    });

    $('#add-modal-contact').on('click', function () {
        titleModal.html('Добавить контакт')
        formModal.attr('action', $(this).data('route'))
        inputPhone.val('');
        inputEmail.val('');
        inputPost.val('');
        inputSurname.val('');
        inputFirstname.val('');
        inputSecondname.val('');
    });
})();
