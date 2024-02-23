<footer class="footer">
    <div class="container-xl pb-4">
        <div class="row">
            <div class="col-lg-6">
                <div class="heading">NORDI HOME</div>
                <div class="footer-description">
                    <div>
                        <p>Магазин NORDI HOME занимается продажей и доставкой мебели ИКЕА из Европы под ключ для вашего
                            удобства.</p>
                        <div>
                            <p style="color: var(--bs-gold);">Адреса магазинов:<br><span>Калининград</span>,
                                <span>ул. Советский проспект 103А корпус 1</span><br>Калининград,
                                ул. Батальная 18, 2 этаж</p>
                        </div>
                        <div style="color: var(--bs-gold);">ПН-ПТ - 10:00-19:00<br>СБ-ВС - 11:00-18:00</div>
                    </div>
                    <div>
                        <div>
                            <p>тел. <a href="tel:+74012373730"  style="color: var(--bs-gold);">+7(4012)37-37-30</a><br>+7 906 210-85-05 - телефон для
                                мессенджеров</p>
                            <p>ООО «Негоциант», ИНН 3906396090, КПП 390601001, <span>236023</span>
                                <span>Калининград</span>, <span>ул Советский проспект 103А корпус 1</span></p>
                        </div>
                    </div>

                    <p>Дисклеймер: Сайт NORDI HOME (nordihome.ru) не имеет отношения к компании ИКЕА в России, не
                        отражает ее концепцию, не связан с ikea.com, ikea.ru, IKEA Systems B.V. Продукция или ее
                        изображения, опубликованные на страницах, являются объектом прав интеллектуальной собственности
                        Inter IKEA Systems B. V. Все ссылки и описания предназначены только для удобства пользователя и
                        созданы для популяризации продукции IKEA.</p>
                    <p>Предоставляемая информация носит информационный характер. Не является публичной офертой,
                        определяемой положениями статьи 437 ГК РФ. Все права на публикуемые здесь материалы принадлежат
                        ООО «Негоциант».</p>
                </div>

            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-5">
                <div class="row">
                    @foreach(\App\Modules\Shop\MenuHelper::getFooterMenu() as $column)
                        <div class="col-lg-6 px-2">
                            <div class="menu-column">
                                <div class="heading">{{ $column['title'] }}</div>
                                <ul class="menu">
                                @foreach($column['items'] as $item)
                                        <li><a href="{{ $item['route'] }}">{{ $item['name'] }}</a></li>
                                @endforeach

                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="about pt-4 pb-3 text-center">
        <p>2022 - {{date('Y')}} | Разработано <a href="https://website39.site" title="Разработка интернет-магазинов" target="_blank">Веб-студия Web39</a></p>
    </div>

</footer>
