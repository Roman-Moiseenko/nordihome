(function () {
    "use strict";

    let url = $('#dadata').data('url');
    let bank = $('#dadata').data('bank');
    let token = $('#dadata').data('token');

    let inputInn = $('#input-inn');
    let inputOgrn = $('#input-ogrn');
    let inputBik = $('#input-bik');

    const find_company = (str) => {
        if (str.target.value === '') return;

        let options = {
            method: "POST",
            mode: "cors",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": "Token " + token
            },
            body: JSON.stringify({query: str.target.value})
        }

        fetch(url, options)
            .then(response => response.text())
            .then(result => {
                let data = JSON.parse(result).suggestions[0].data;
                let address = data.address.data
                //Адрес
                $("#input-legal_address\\[post\\]").val(address.postal_code)
                $("#input-legal_address\\[region\\]").val(address.region_with_type)
                $('#input-legal_address\\[address\\]').val(address.source)
                $("#input-actual_address\\[post\\]").val(address.postal_code)
                $("#input-actual_address\\[region\\]").val(address.region_with_type)
                $('#input-actual_address\\[address\\]').val(address.source)
                //Название и налоговая
                $('#input-full_name').val(data.name.full_with_opf)
                $('#input-short_name').val(data.name.short_with_opf)
                $('#input-inn').val(data.inn)
                $('#input-kpp').val(data.kpp)
                $('#input-ogrn').val(data.ogrn)
                //Руководитель
                $('#input-post').val(data.management.post)
                let fio = data.management.name.split(' ', 3)
                $('#input-chief\\[surname\\]').val(fio[0])
                $('#input-chief\\[firstname\\]').val(fio[1])
                $('#input-chief\\[secondname\\]').val(fio[2])

            })
            .catch(error => console.log("error", error));

    };

    const find_bank = (str) => {
        if (str.target.value === '') return;
        let options = {
            method: "POST",
            mode: "cors",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": "Token " + token
            },
            body: JSON.stringify({query: str.target.value})
        }

        fetch(bank, options).then(response => response.text()).then(result => {
            let data = JSON.parse(result).suggestions[0].data
            console.log('ИНН банка', data.inn);
            $('#input-bank_name').val(data.name.payment)
            $('#input-corr_account').val(data.correspondent_account)

        }).catch(error => console.log("error", error));
    }
    inputInn.on('change', find_company);
    inputOgrn.on('change', find_company);
    inputBik.on('change', find_bank);

})();
