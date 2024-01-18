<div class="parser-user-form">
    <div class="parser-flex">
        <div>
            <input type="radio" id="user-type-person" name="user-type" value="0" {person}>
            <label class="label-pointer" for="user-type-person">Физическое лицо</label>
        </div>
        <div>

            <input type="radio" id="user-type-company" name="user-type" value="1" {company}>
            <label class="label-pointer" for="user-type-company">Юридическое лицо</label>
        </div>
    </div>
    <div id="hidden-company-block" class="parser-flex">
        <div class="parser-col-6">
            <label for="inn">ИНН компании <span class="red">*</span></label>
            <input class="user-form-input" type="text" id="inn" name="inn" placeholder="ИНН" value="{inn}"/>
        </div>
        <div class="parser-col-6">
            <label for="company-name">Название компании <span class="red">*</span></label>
            <input class="user-form-input" type="text" id="company-name" name="company-name"
                   placeholder="Название компании" value="{company_name}"/>
        </div>
    </div>
    <div class="parser-flex">
        <div class="parser-col-4">
            <label for="user-surname">Фамилия <span class="red">*</span></label>
            <input class="user-form-input" type="text" id="user-surname" name="user-surname" placeholder="Фамилия"
                   value="{user_surname}"/>
        </div>
        <div class="parser-col-4">
            <label for="user-firstname">Имя <span class="red">*</span></label>
            <input class="user-form-input" type="text" id="user-firstname" name="user-firstname" placeholder="Имя"
                   value="{user_firstname}"/>
        </div>
        <div class="parser-col-4">
            <label for="user-middlename">Отчество</label>
            <input class="user-form-input" type="text" id="user-middlename" name="user-middlename" placeholder="Отчество"
                   value="{user_middlename}"/>
        </div>

    </div>
    <div class="parser-flex">
        <div class="parser-col-6">
            <label for="user-phone">Телефон <span class="red">*</span></label>
            <input class="user-form-input" type="text" id="user-phone" name="user-phone"
                   placeholder="+7(999) 999 9999" value="{user_phone}" data-mask="+7(999) 999 9999"/>
        </div>
        <div class="parser-col-6">
            <label for="user-email">Электронная почта <span class="red">*</span></label>
            <input class="user-form-input" type="text" id="user-email" name="user-email"
                   placeholder="email@email.ru" value="{user_email}"/>
        </div>
    </div>
    <div class="parser-flex parser-delivery">
        <div>
            <input type="radio" id="delivery-region" name="delivery-type" value="100">
            <label class="label-pointer" for="delivery-region">Доставка в регионы <span class="red">*</span></label>
        </div>
        <div>

            <input type="radio" id="delivery-local" name="delivery-type" value="101">
            <label class="label-pointer" for="delivery-local">Самовывоз <span class="red">*</span></label>
        </div>
    </div>
    <div id="delivery-hidden-region">

        <label for="user-address">Адрес доставки <span class="red">*</span></label>
        <input class="user-form-input" id="user-address" name="user-address" value="{user_address}"/>
    </div>
    <div id="delivery-hidden-local">
        <div>
            <input type="radio" id="delivery-local-1" name="delivery-shop" value="Калининград, ул. Советский проспект 103А корпус 1">
            <label class="label-pointer" for="delivery-local-1">Калининград, ул. Советский проспект 103А корпус 1</label>
        </div>
        <div>
            <input type="radio" id="delivery-local-2" name="delivery-shop" value="Калининград, ул. Батальная 18, 2 этаж">
            <label class="label-pointer" for="delivery-local-2">Калининград, ул. Батальная 18, 2 этаж</label>
        </div>
    </div>
    <div class="parser-comment">
        <label for="user-comment">Комментарий (необязательно)</label>
        <textarea id="user-comment" name="user-comment"></textarea>
    </div>
    <div>
        <input class="user-form-input" type="checkbox" id="user-agree" name="user-agree">
        <label class="label-pointer" for="user-agree">Я согласен на обработку моих <a
                href="https://euroikea.com/politika-obrabotki-personalnyh-dannyh/" target="_blank">персональных
                данных</a></label>
    </div>
    <div id="error-block" style="display:none;">
        <span style="color: #5e0404">Не все поля заполнены!</span>
    </div>
    <div>
        <button id="complete-button" onclick="ym(88113821,'reachGoal','parser-order'); return true;">Заказать</button>
    </div>
</div>

<script>
    ymaps.ready(init);
    function init() {
        let suggestView = new ymaps.SuggestView('user-address');
        suggestView.events.add('select', function (event) {
            let selected = event.get('item').value;
            ymaps.geocode(selected, {
                results: 1
            }).then(function (res) {
                return ymaps.geocode(res.geoObjects.get(0).geometry.getCoordinates(), {
                    kind: 'district',
                    results: 10
                }).then(function (res) {
                    let founded = res['metaData']['geocoder']['found'];
                    $('label.suggest .description').html("");
                    for (i = 0; i <= founded - 1; i++) {
                        var info = res.geoObjects.get(i).properties.getAll();
                        let name = info['name'];
                        if (name.search('район') != -1) {
                            name = name.replace(' район', '');
                        }
                    }
                });
            });
        });
        document.getElementsByTagName('ymaps')[0].style.top = document.getElementsByTagName('ymaps')[0].style.top.match(/d+/) * 1 + 5 + 'px';
        document.getElementsByTagName('ymaps')[0].style.left = document.getElementsByTagName('ymaps')[0].style.left.match(/d+/) * 1 - 1 + 'px';
    }
</script>
