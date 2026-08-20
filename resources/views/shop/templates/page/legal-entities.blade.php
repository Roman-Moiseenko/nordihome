<!--template:Юридическим лицам-->
@extends('layouts.main')

@section('main')
    pages
@endsection

@section('title', $title)
@section('description', $description)

@section('content')
    <div class="container-xl">
        <h1 class="my-4">{{ $page->name }}</h1>
    </div>
    <div class="container-xl">
        <div class="mt-4">
            {!! $page->text !!}
        </div>
    </div>
    <div class="container-xl">
        <div class="page-ur-licam">
            <div class="row">
                <div class="col-md-12 col-lg-5 col-xl-6 bg-f2f2f2 b-radius-left_16 m-b_10">
                    <div class="p-block_15">
                        <h4>Товары и мебель из Европы<br>для оптовых и розничных закупок <span class="t-color_orange">с 22% НДС</span></h4>
                        <p>Приглашаем к сотрудничеству: заведения общественного питания, гостиницы, хостелы и отели, малый и средний бизнес, государственные и муниципальные организации.</p>
                        <div class="btn btn-orange_1 m-t_20" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#ur-licam-modal">Стать клиентом</div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-7 col-xl-6 padding_0"><img src="/images/pages/legal-entities/ur-licam01.jpg" alt="Товары и мебель из Европы для оптовых и розничных закупок" class="d-block b-radius-right_16 width_100 m-b_10"></div>
            </div>
            <div class="m-t_50">
                <h3 class="t-a_center m-b_10 m-t_0">Оснащаем бизнес-пространства под ключ</h3>
                <div class="t-a_center m-b_50">С 2020 года создаём готовые интерьерные решения для коммерческих пространств.</div>
                <div class="row">
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <img src="/images/pages/legal-entities/ur-licam02.jpg" alt="Офисы и рабочие пространства" class="b-radius_16 d-block width_100">
                        <div class="t-a_center m-t_20 m-b_10"><b>Офисы и рабочие пространства</b></div>
                        <div class="f-z_16 t-a_center">Мебель для кабинетов, переговорных и зон отдыха.</div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <img src="/images/pages/legal-entities/ur-licam03.jpg" alt="Квартиры под сдачу" class="b-radius_16 d-block width_100">
                        <div class="t-a_center m-t_20 m-b_10"><b>Квартиры под сдачу (инвестиционная недвижимость)</b></div>
                        <div class="f-z_16 t-a_center">Полная комплектация квартир для аренды: от мебели до декора и посуды.</div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <img src="/images/pages/legal-entities/ur-licam04.jpg" alt="Заведения общественного питания" class="b-radius_16 d-block width_100">
                        <div class="t-a_center m-t_20 m-b_10"><b>Заведения общественного питания</b></div>
                        <div class="f-z_16 t-a_center">Интерьерные решения для ресторанов, кафе, кофеен.</div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <img src="/images/pages/legal-entities/ur-licam05.jpg" alt="Ритейл и коммерческие помещения" class="b-radius_16 d-block width_100">
                        <div class="t-a_center m-t_20 m-b_10"><b>Ритейл и коммерческие помещения</b></div>
                        <div class="f-z_16 t-a_center">Оснащение магазинов, шоурумов и студий: витрины, стойки, зоны ожидания и т.п.</div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <img src="/images/pages/legal-entities/ur-licam06.jpg" alt="Образовательные и общественные пространства" class="b-radius_16 d-block width_100">
                        <div class="t-a_center m-t_20 m-b_10"><b>Образовательные и общественные пространства</b></div>
                        <div class="f-z_16 t-a_center">Безопасные и функциональные решения для школ, студий, центров развития.</div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <img src="/images/pages/legal-entities/ur-licam07.jpg" alt="Апартаменты, отели" class="b-radius_16 d-block width_100">
                        <div class="t-a_center m-t_20 m-b_10"><b>Апартаменты, отели</b></div>
                        <div class="f-z_16 t-a_center">Мебель для комфортного проживания гостей.</div>
                    </div>
                </div>
            </div>
            <div class="p-block_50 bg-blue t-a_center b-radius_16 m-t_50 m-b_50">
                <h3 class="m-t_0">Не нашли свой формат бизнеса?</h3>
                <div class="m-b_30">Подберем решение под ваш проект за 24 часа</div>
                <a href="#" class="btn btn-orange_1 m-t_20" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#ur-licam-modal">Получить бесплатную консультацию</a>
                <div class="modal fade show" id="ur-licam-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content f-z_16">
                            <h4 class="t-a_center m_0">Оставить заявку</h4>
                            <p class="t-a_center">Приглашаем Вас к сотрудничеству и предлагаем гибкие начальные условия.</p>
                            <div id="legal-entities-form" class="feedback-form">
                                <form>
                                    <input type="hidden" name="form" value="Заявка со страницы юридическим лицам"/>
                                    <div>
                                        <label>
                                            <input name="name" type="text" required placeholder="Имя*"/>
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <input name="phone" type="tel" required placeholder="Телефон*"/>
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <input name="mail" type="email" required placeholder="Email*"/>
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <input name="telegram" placeholder="Ник в Телеграм: @username"/>
                                        </label>
                                    </div>
                                    <div>
                                        <label><select class="width_100"><option value="">—Выберите вариант—</option><option value="Позвонить по телефону">Позвонить по телефону</option><option value="Написать на почту">Написать на почту</option><option value="Написать в Телеграм">Написать в Телеграм</option><option value="Написать в Макс">Написать в Макс</option></select></label>
                                    </div>
                                    <div>
                                        <label>
                                            <textarea placeholder="Комментарий"></textarea>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="f-z_14">
                                            <input type="checkbox" name="agreement" value="Принимаю согласие"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
                                        </label>
                                    </div>
                                    <div>
                                        <label><button class="btn-form btn btn-black" type="button">Оставить заявку</button></label>
                                    </div>
                                </form>
                            </div>
                            <div id="legal-entities-form-callback" class="form-send-message" style="display: none">
                                Ваше сообщение отправлено. Менеджер скоро свяжется с Вами.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <h3 class="m-t_0 t-a_center m-b_50">Плюсы работы с нами:</h3>
                <div class="row">
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <div class="bg-f2f2f2 p-block_20 b-radius_16 height_stretch">
                            <div class="t-a_center"><img src="/images/pages/legal-entities/ur-licam08.png" alt="10 000+ товаров и готовых решений для вашего бизнеса"></div>
                            <div class="t-a_center m-b_20"><b>10 000+ товаров и готовых решений для вашего бизнеса</b></div>
                            <ul class="f-z_16">
                                <li>Большой выбор оригинальных товаров: от посуды до мебели и освещения.</li>
                                <li>Регулярные поставки со всей Европы: 1-2 раза в месяц.</li>
                            </ul>
                            <a href="/catalog/" class="btn btn-white d-block m-t_20 width_100">Смотреть каталог</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <div class="bg-f2f2f2 p-block_20 b-radius_16 height_stretch">
                            <div class="t-a_center"><img src="/images/pages/legal-entities/ur-licam09.png" alt="Индивидуальная ценовая политика"></div>
                            <div class="t-a_center m-b_20"><b>Индивидуальная ценовая политика</b></div>
                            <ul class="f-z_16">
                                <li>Все счета-фактуры с 22% НДС, который можно взять в зачет при использовании или перепродаже товара.</li>
                                <li>Гибкий курс и система скидок для постоянных клиентов.</li>
                                <li>Возможность разделить оплату на несколько частей: выкуп, доставка, растаможка на территории РФ.</li>
                                <li>Оплата по факту выполнения каждой услуги для максимального удобства.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <div class="bg-f2f2f2 p-block_20 b-radius_16 height_stretch">
                            <div class="t-a_center"><img src="/images/pages/legal-entities/ur-licam10.png" alt="Доставка по всей России с выгодными тарифами"></div>
                            <div class="t-a_center m-b_20"><b>Доставка по всей России с выгодными тарифами от 60 руб./кг</b></div>
                            <ul class="f-z_16">
                                <li>Привезём ваш заказ независимо от объема и веса: чем больше вес, тем выгоднее стоимость доставки. <a href="/page/tarify-i-usloviia-dostavki/" class="t-color_orange">Подробнее о тарифах.</a></li>
                                <li>Предоставляем возможность хранения заказов на нашем складе для удобства бизнеса.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <div class="bg-f2f2f2 p-block_20 b-radius_16 height_stretch">
                            <div class="t-a_center"><img src="/images/pages/legal-entities/ur-licam11-1.png" alt="Персональное сопровождение"></div>
                            <div class="t-a_center m-b_20"><b>Персональное сопровождение на всех этапах сотрудничества</b></div>
                            <ul class="f-z_16">
                                <li>Работа с персональным менеджером и помощь в подборе мебели.</li>
                                <li>Предоставление 3D-моделей  мебели для проектирования и дизайна (по запросу).</li>
                                <li>Подробная отчётность (фото/видео) по каждой стадии выполнения заказа.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <div class="bg-f2f2f2 p-block_20 b-radius_16 height_stretch">
                            <div class="t-a_center"><img src="/images/pages/legal-entities/ur-licam12.png" alt="Оплата по счету и полный пакет документов"></div>
                            <div class="t-a_center m-b_20"><b>Оплата по счету и полный пакет документов</b></div>
                            <ul class="f-z_16">
                                <li>Мы предоставляем договор, акт приема-передачи (торг-12), счет-фактуру.</li>
                                <li>Отправка документов через ЭДО для удобства работы с бухгалтерией.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 m-b_30">
                        <div class="bg-blue p-block_20 b-radius_16 t-a_center  height_stretch d-flex flex-direction_column justify-content-center">
                            <div class="m-b_30">Рассчитайте свой первый заказ из ИКЕА на нашем сайте</div>
                            <a href="/ikea/" class="btn btn-white">Открыть калькулятор</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-t_50 m-b_50">
                <h3 class="m-t_0 t-a_center m-b_50">Что мы предлагаем</h3>
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <img src="/images/pages/legal-entities/ur-licam13.jpg" alt="Огромный выбор товаров напрямую из Европы" class="d-block b-radius_15 m-b_20 width_100">
                        <div class="f-z_16 t-a_center m-b_20">Огромный выбор товаров напрямую из Европы с доставкой по РФ или самовывоз в Калининграде.</div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <img src="/images/pages/legal-entities/ur-licam14.jpg" alt="Взаимовыгодное сотрудничество с дизайнерами" class="d-block b-radius_15 m-b_20 width_100">
                        <div class="f-z_16 t-a_center m-b_20">Взаимовыгодное сотрудничество с дизайнерами и студиями дизайна.</div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <a href="/page/podarocnyi-sertifikat-v-nordi-xoum-nordi-home/"><img src="/images/pages/legal-entities/ur-licam15.jpg" alt="Готовые подарки" class="d-block b-radius_15 m-b_20 width_100"></a>
                        <div class="f-z_16 t-a_center m-b_20">Готовые подарки для коллег и партнеров. Номиналы от 500 до 50 000 рублей - на ваш выбор.</div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <img src="/images/pages/legal-entities/ur-licam16-1.jpg" alt="Девелоперам недвижимости" class="d-block b-radius_15 m-b_20 width_100">
                        <div class="f-z_16 t-a_center m-b_20">Приятные бонусы и спецпредложения для ваших клиентов при обустройстве нового жилья.</div>
                    </div>
                </div>
            </div>
            <div class="m-b_50 m-t_50">
                <h3 class="m-t_0 t-a_center m-b_50">Нам доверяют:</h3>
                <div class="row">
                    <div class="col-lg-6 bg-black b-radius-left_15 m-b_10 page-ur-mrl">
                        <div class="p-block_15 t-a_center height_stretch d-flex flex-direction_column justify-content-center align-items-center">
                            <svg width="255" height="38" viewBox="0 0 255 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.60118 28.0989L5.6001 14.2515L20.9558 24.8772V17.4948L24.3265 19.823V31.3205L8.95998 20.6882L8.96429 28.0989H5.60118Z" fill="white"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M28.4592 28.0989V18.4187L16.8129 10.3733L11.4721 14.2069L8.52002 12.1678L16.7784 6.23975L31.8299 16.6384V28.0989H28.4592Z" fill="#D7B56D"></path>
                                <path d="M0.901134 0.0595703H37.2987V37.5009H0.131836V0.0595703H0.901134ZM35.7591 1.60953H1.67043V35.9498H35.7591V1.60953Z" fill="white"></path>
                                <path d="M66.6516 29.155H63.6856V19.2156H51.9346V29.155H48.73V6.04684H51.9346V15.9873H63.6856V6.04684H66.8902V29.155H66.6516ZM112.136 32.482H109.675V26.5906H111.534L120.739 5.11084L130.486 26.5906H132.285V32.482H129.585V29.155H112.375V32.482H112.136ZM114.55 26.5906H127.342L120.785 11.7353L114.55 26.5906ZM70.3758 17.6015C70.3758 14.2613 71.4782 11.4467 73.6839 9.1588C74.7884 8.01431 76.04 7.15512 77.4386 6.58233C78.8372 6.01063 80.3807 5.72423 82.0691 5.72423C83.7465 5.72423 85.2813 6.00185 86.6723 6.55709C88.0666 7.11342 89.317 7.94737 90.4205 9.05894C91.5337 10.1716 92.3702 11.4412 92.9279 12.8677C93.4846 14.2909 93.7623 15.8688 93.7623 17.6015C93.7623 19.2902 93.4846 20.8451 92.9279 22.2628C92.3713 23.6816 91.5348 24.9644 90.4205 26.1089C89.3061 27.2314 88.0524 28.0742 86.6581 28.636C85.265 29.1967 83.7356 29.4776 82.0691 29.4776C78.7316 29.4776 75.9376 28.366 73.6883 26.144C72.5827 25.0302 71.7537 23.7606 71.2026 22.3352C70.6514 20.9098 70.3758 19.3319 70.3758 17.6015ZM73.7406 17.6015C73.7406 20.0814 74.5423 22.1816 76.1457 23.9011C76.9463 24.7383 77.8395 25.3671 78.8242 25.7841C79.8089 26.2021 80.8916 26.4106 82.0691 26.4106C83.2999 26.4106 84.4143 26.2021 85.4109 25.7841C86.4065 25.366 87.2888 24.7383 88.0546 23.9033C88.8367 23.0628 89.4227 22.1191 89.8116 21.0733C90.2015 20.0243 90.3976 18.8667 90.3976 17.6015C90.3976 15.0788 89.5959 12.9796 87.9936 11.3018C87.193 10.4635 86.2998 9.83584 85.314 9.41776C84.3293 8.99969 83.2466 8.7912 82.0691 8.7912C80.8687 8.7912 79.7729 8.99749 78.7817 9.41008C77.7915 9.82267 76.9016 10.4426 76.1119 11.27C75.3222 12.0974 74.7296 13.0378 74.3342 14.0901C73.9388 15.1446 73.7406 16.3154 73.7406 17.6015ZM101.679 17.0375H104.639C106.99 16.9892 108.621 15.081 108.621 12.9785C108.621 11.6661 108.212 10.6862 107.397 10.0399C106.563 9.37936 105.297 9.04907 103.597 9.04907C103.447 9.04907 102.726 9.05785 101.805 9.07211L99.9842 9.10723V29.155H96.7807V6.04684H97.0181C97.5922 6.04684 98.1205 6.04465 98.5976 6.04026C99.1335 6.03477 99.6879 6.02709 100.255 6.01612C100.758 6.00734 101.361 5.99856 101.941 5.99417L103.757 5.9821C109.243 5.9821 111.986 8.304 111.986 12.9467C111.986 16.6435 109.484 19.6041 105.691 19.5898L103.625 19.5953L101.679 17.0375ZM135.14 28.9487V6.04684H138.185V21.6845L154.389 5.7385V29.155H151.344V13.3132L135.14 29.5259V28.9487Z" fill="white"></path>
                                <path d="M227.962 6.01179L221.175 21.215H221.143L212.837 6.01179H210.769L220.335 23.3317C219.236 26.0991 218.299 27.4981 216.295 27.4981C215.422 27.4981 214.292 27.2051 213.354 26.392L212.708 27.9875C213.58 28.8983 215.197 29.2889 216.327 29.2889C219.786 29.2889 220.755 26.6203 222.177 23.3964L229.901 6.01179H227.962ZM166.825 29.1265H168.99L176.003 19.1323H176.067L183.048 29.1265H185.245L177.232 17.5697L185.181 6.01179H183.048L176.067 16.1037H176.036L169.023 6.01179H166.857L174.84 17.5697L166.825 29.1265ZM186.63 17.573C186.63 14.2043 187.722 11.3864 189.908 9.11936C192.093 6.85123 194.836 5.71771 198.136 5.71771C201.414 5.71771 204.146 6.84025 206.331 9.08534C208.539 11.3096 209.643 14.1384 209.643 17.573C209.643 20.9417 208.539 23.7706 206.331 26.0596C204.146 28.3057 201.414 29.4283 198.136 29.4283C194.858 29.4283 192.115 28.3167 189.908 26.0936C187.722 23.8693 186.63 21.0295 186.63 17.573ZM188.53 17.573C188.53 20.4348 189.449 22.8456 191.285 24.8053C193.142 26.7421 195.427 27.711 198.136 27.711C200.934 27.711 203.228 26.7421 205.021 24.8053C206.834 22.8456 207.742 20.4348 207.742 17.573C207.742 14.6454 206.813 12.2346 204.954 10.3407C203.119 8.40282 200.846 7.43499 198.136 7.43499C195.404 7.43499 193.121 8.40282 191.285 10.3407C189.449 12.2566 188.53 14.6662 188.53 17.573ZM234.61 29.1309H232.807V5.56299L243.887 19.0577L254.508 5.56299V29.1309H252.706V10.8312L243.815 22.2453L234.61 10.931V29.1309Z" fill="white"></path>
                            </svg>
                            <div class="m-t_20"><i>“<b>Наша цель</b>&nbsp;— обеспечить клиентам доступ к известным и качественным мебельным брендам, которые в данный момент недоступны в России.<br>Мы стремимся сделать покупку мебели и товаров для дома легкой и приятной для каждого.”</i></div>
                        </div>
                    </div>
                    <div class="col-lg-6 padding_0 m-b_10">
                        <img src="/images/pages/legal-entities/ur-licam17.jpg" alt="Команда Норди Хоум" class="d-block b-radius-right_15 width_100">
                    </div>
                </div>
            </div>
            <div>тут подключить вывод рейтинга</div>
            <div class="bg-black b-radius_15 m-t_50 m-b_50 p-block_30">
                <div class="row">
                    <div class="col-md-12 col-lg-6">
                        <h3 class="t-color_orange">Приглашаем Вас к сотрудничеству и предлагаем гибкие начальные условия</h3>
                        <div class="m-b_20">Оставьте заявку, чтобы обсудить или свяжитесь с нами по телефону: <a href="{!! $contacts['phone']->url !!}" class="t-color_white">{{ phone( $contacts['phone']->url ) }}</a></div>
                        <div class="m-b_20">Мы открыты к сотрудничеству и рады обсудить это на личной встрече по адресу: г. Калининград, ул. Советский проспект 103А, корпус 1</div>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <div class="bg-white b-radius_15 p-block_30 form-urlica">
                            <h3>Заполните заявку</h3>
                            <div id="legal-entities_1-form" class="feedback-form">
                                <form>
                                    <input type="hidden" name="form" value="Заявка со страницы юридическим лицам"/>
                                    <div>
                                        <label>
                                            <input name="name" type="text" required placeholder="Имя*"/>
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <input name="phone" type="tel" required placeholder="Телефон*"/>
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <input name="mail" type="email" required placeholder="Email*"/>
                                        </label>
                                    </div>
                                    <div>
                                        <label>
                                            <input name="telegram" placeholder="Ник в Телеграм: @username"/>
                                        </label>
                                    </div>
                                    <div>
                                        <label><select class="width_100"><option value="">—Выберите вариант—</option><option value="Позвонить по телефону">Позвонить по телефону</option><option value="Написать на почту">Написать на почту</option><option value="Написать в Телеграм">Написать в Телеграм</option><option value="Написать в Макс">Написать в Макс</option></select></label>
                                    </div>
                                    <div>
                                        <label>
                                            <textarea placeholder="Комментарий"></textarea>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="f-z_14">
                                            <input type="checkbox" name="agreement" value="Принимаю согласие"> Я <a href="/page/soglasie-na-obrabotku-personalnyx-dannyx" target="_blank">согласен</a> на обработку персональных данных. Подробнее об этом в <a href="/page/politika-obrabotki-personalnyx-dannyx" target="_blank">политике конфиденциальности</a>
                                        </label>
                                    </div>
                                    <div>
                                        <label><button class="btn-form btn btn-orange width_100" type="button">Оставить заявку</button></label>
                                    </div>
                                </form>
                            </div>
                            <div id="legal-entities_1-form-callback" class="form-send-message" style="display: none">
                                Ваше сообщение отправлено. Менеджер скоро свяжется с Вами.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
