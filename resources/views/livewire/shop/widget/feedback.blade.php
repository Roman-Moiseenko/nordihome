<div>
    <div class="container-xl mt-5">
        <div class="row">
            <div class="col-lg-6">
                <h2>Контакты</h2>
                <div class="">Мы всегда рады ответить на все ваши вопросы,<br>принять пожелания и предложения по работе нашего сервиса</div>
                <div class="my-4 d-flex justify-content-between">
                    <i class="fa-thin fa-phone-office fs-1"></i>
                    <div style="display:flex; margin: auto 0;">
                        <a href="tel:+74012373730" class="fs-4 me-3">+7 (4012) 37-37-30</a>
                        <a href="https://wa.me/+79062108505?text=Здравствуйте!%20Хочу%20мебель%20из%20ИКЕА!" class="me-3">
                            <img src="/images/pages/whatsapp.png" style="width: 100%;">
                        </a>
                        <a href="https://t.me/nordi_home">
                            <img src="/images/pages/telegram.png" style="width: 100%;">
                        </a>
                    </div>
                </div>
                <hr/>
                <div class="my-4 d-flex justify-content-between">
                    <i class="fa-thin fa-planet-ringed fs-1"></i>
                    <div style="display:flex; margin: auto 0;">
                        <a href="https://vk.com/nordihome" class="me-3">
                            <img src="/images/pages/vk.png" style="width: 100%;">
                        </a>
                        <a href="https://www.avito.ru/user/767a54a084b8b382bc26e36a914ec5f7/profile/all?sellerId=767a54a084b8b382bc26e36a914ec5f7">
                            <img src="/images/pages/avito.png" style="width: 100%;">
                        </a>
                    </div>
                </div>
                <hr/>

                <div class="my-4 d-flex justify-content-between">
                    <i class="fa-thin fa-square-envelope fs-1"></i>
                    <div>
                        <a href="mailto:info@nordihome.ru" class="btn btn-outline-dark rounded-pill">info@nordihome.ru</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2>Форма обратной связи</h2>
                <div @if($send) style="display: none" @endif>
                    <div class="form-floating mt-2">
                        <input type="email" class="form-control mask-email" placeholder="Электронная почта" required=""
                               wire:model="email" autocomplete="off">
                        <label for="email">Электронная почта</label>
                    </div>
                    <div class="form-floating mt-3">
                        <input type="text" class="form-control mask-phone" placeholder="Телефон" required=""
                               wire:model="phone" autocomplete="off">
                        <label for="phone">Телефон</label>
                    </div>
                    <div class="form-floating mt-3">
                        <textarea id="message" class="form-control" placeholder="Сообщение" required="" rows="8" style="height: 150px"
                                wire:model="text"
                        ></textarea>
                        <label for="message">Сообщение</label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"  required=""  wire:model="check">
                        <label class="form-check-label" for="flexCheckDefault">
                            Я согласен на обработку персональных данных
                        </label>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <span class="btn btn-dark fs-5 py-2 px-3" wire:click="send_message">Отправить</span>
                    </div>
                </div>
                <div @if($send == false) style="display: none" @endif class="feedback-send">
                    <div>
                        Ваше сообщение отправлено!<br>
                        Менеджер свяжется с Вами в ближайшее время!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
