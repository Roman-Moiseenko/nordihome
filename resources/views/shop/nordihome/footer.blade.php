<footer id="footer" class="p-t_50 p-b_50 bg-black">
    <div class="container-xl">
        <div class="row justify-content-between">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="heading f-w_600 f-z_23 m-b_20">NORDI HOME</div>
                <div class="f-z_13">
                    <div>
                        <p>Магазин NORDI HOME занимается продажей и доставкой мебели ИКЕА из Европы под ключ для вашего удобства.</p>
                        <div>
                            <p class="t-color_orange">Адреса магазинов:<br><span>Калининград</span>, <span>ул. Советский проспект 103А корпус 1</span></p>
                        </div>
                        <div class="t-color_orange">ПН-ПТ - 10:00-19:00<br>СБ-ВС - 11:00-18:00</div>
                    </div>
                    <div>
                        <div>
                            <p>тел. <a href="tel:88007008179">8 (800) 700-81-79</a><br>тел. <a href="tel:+74012373730">+7(4012)37-37-30</a><br>+7 906 210-85-05 - телефон для мессенджеров<br><a href="mailto:partnership@nordihome.ru">partnership@nordihome.ru</a> - по вопросам сотрудничества</p>
                            <p>ООО «Негоциант», ИНН 3906396090, КПП 390601001, ОГРН 1203900013602, 236023 Калининград, ул Советский проспект, 103А корпус 1</p>
                        </div>
                    </div>

                    <noindex><p>Дисклеймер: Сайт NORDI HOME (nordihome.ru) не имеет отношения к компании ИКЕА в России, не отражает ее концепцию, не связан с ikea.com, ikea.ru, IKEA Systems B.V. Продукция или ее изображения, опубликованные на страницах, являются объектом прав интеллектуальной собственности Inter IKEA Systems B. V. Все ссылки и описания предназначены только для удобства пользователя и созданы для популяризации продукции IKEA.</p>
                        <p>Предоставляемая информация носит информационный характер. Не является публичной офертой, определяемой положениями статьи 437 ГК РФ. Все права на публикуемые здесь материалы принадлежат ООО «Негоциант». При использовании материалов, активная ссылка на сайт nordihome.ru обязательна.</p>
                    </noindex>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-2">888</div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                <div class="heading f-w_600 m-b_20">МЕНЮ</div>
                @if(isset($menus['menu-footer01']))
                <ul class="footer-menu">
                    @foreach($menus['menu-footer01']['items'] as $item)
                        <li><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
                    @endforeach
                </ul>
                @else
                Меню не найдено
                @endif
            </div>
            <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                <div class="heading f-w_600 m-b_20">ДЛЯ КЛИЕНТА</div>
                @if(isset($menus['menu-footer07']))
                <ul class="footer-menu">
                    @foreach($menus['menu-footer02']['items'] as $item)
                        <li><a href="{{ $item['url'] }}">{{ $item['name'] }}</a></li>
                    @endforeach
                </ul>
                @else
                    Меню не найдено
                @endif
            </div>
        </div>
    </div>
    <div class="about pt-4 pb-3 text-center">
        <p>2022 - {{date('Y')}} | Разработано <a href="https://website39.site" title="Разработка CRM и интернет-магазинов" target="_blank">Веб-студия Web39</a></p>
    </div>

</footer>
