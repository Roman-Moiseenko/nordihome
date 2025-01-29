<footer class="footer">
    <div class="container-xl pb-4">
        <div class="row">
            <div class="col-lg-6">
                <div class="heading">NEW BALANCE</div>
                <div style="color: #ff5555">САЙТ В РАЗРАБОТКЕ!</div>
                <div class="description">
                    <div>
                        <p>Магазин NB RUSSIA занимается продажей и доставкой одежды и обуви бренда New Balance из Европы.</p>
                        <div>
                            В данный момент работает только онлайн-продажа
                        </div>

                    </div>
                    <div>
                        <div>
                            <p>тел. <a href="tel:+74012373730"  style="color: var(--bs-gold);">+7(9..) ... ....</a></p>
                            <p>ООО «Кёнигс.ру», ИНН 3906396773, КПП 390601001, <span>236001</span>
                                <span>Калининград</span></p>
                        </div>
                    </div>
                    <p>Предоставляемая информация носит информационный характер. Не является публичной офертой,
                        определяемой положениями статьи 437 ГК РФ. Все права на публикуемые здесь материалы принадлежат
                        ООО «Кёнигс.ру».</p>
                </div>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-5">
                <div class="row">
                    @foreach(\App\Modules\NBRussia\Helper\MenuHelper::getFooterMenu() as $column)
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
        <p>2022 - {{date('Y')}} | Разработано <a href="https://website39.site" title="Разработка CRM и интернет-магазинов" target="_blank">Веб-студия Web39</a></p>
    </div>

</footer>
