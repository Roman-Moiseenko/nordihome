(function () {

    const panels = document.querySelectorAll('.mega-menu-panel');
    const buttons = document.querySelectorAll('.header-menu-buttons-item');
    const body = document.body; // Ссылка на элемент body

// Функция закрытия всех выпадающих меню
    function closeAll() {
        panels.forEach(p => p.classList.remove('show'));
        buttons.forEach(b => b.classList.remove('active'));
        body.classList.remove('no-scroll'); // Всегда убираем класс скролла при общем закрытии
    }

// Логика главных кнопок (Каталог / Комнаты) строго по КЛИКУ
    buttons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const targetId = btn.getAttribute('data-target');
            const targetPanel = document.getElementById(targetId);
            const isAlreadyOpen = targetPanel.classList.contains('show');

            // Закрываем другие меню, чтобы не накладывались
            closeAll();

            // Если меню до этого было закрыто, то открываем его
            if (!isAlreadyOpen) {
                btn.classList.add('active');
                targetPanel.classList.add('show');
                body.classList.add('no-scroll'); // Добавляем класс блокировки скролла
            }

            // Останавливаем всплытие события, чтобы клик по кнопке не засчитывался как клик вне области
            e.stopPropagation();
        });
    });

// Логика внутренних вкладок при НАВЕДЕНИИ
    panels.forEach(panel => {
        const tabs = panel.querySelectorAll('.nav-pills-custom .nav-link');
        tabs.forEach(tab => {
            tab.addEventListener('mouseenter', () => {
                // Сбрасываем активные классы у соседних вкладок в рамках текущей панели
                tabs.forEach(t => t.classList.remove('active'));
                panel.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Активируем текущую вкладку наведения и её контент справа
                tab.classList.add('active');
                const paneId = tab.getAttribute('data-pane');
                const targetPane = document.getElementById(paneId);
                if(targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            });
        });

        // Останавливаем всплытие кликов внутри самого мега-меню, чтобы оно случайно не закрылось
        panel.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });

// Глобальный обработчик: закрытие при клике в любое свободное место страницы вне меню
    document.addEventListener('click', () => {
        closeAll();
    });

// Дополнительное закрытие по кнопке Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAll();
    });

    // МОБ МЕНЮ
    document.addEventListener('DOMContentLoaded', () => {
        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const body = document.body;

        // Функция для закрытия меню
        const closeMenu = () => {
            menuToggle.classList.remove('active');
            mobileMenu.classList.remove('active');
            body.classList.remove('no-scroll');
        };

        // Открытие / закрытие по клику на кнопку-бургер
        menuToggle.addEventListener('click', (event) => {
            event.stopPropagation(); // Исключаем этот клик из глобального отслеживания
            menuToggle.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            body.classList.toggle('no-scroll');
        });

        // ОСТАНОВКА ЗАКРЫТИЯ И СБРОСА ПРИ КЛИКЕ ВНУТРИ МЕНЮ
        mobileMenu.addEventListener('click', (event) => {
            // Останавливаем всплытие любого клика внутри меню (включая ссылки).
            // Это не даст глобальным обработчикам (из скрипта мега-меню) поймать клик и сбросить стили.
            event.stopPropagation();

            const anchorLink = event.target.closest('a');
            if (anchorLink) {
                const href = anchorLink.getAttribute('href');

                // Если ссылка ведет на якорь текущей страницы (начинается с #)
                if (href && href.startsWith('#')) {
                    // Отменяем стандартный переход браузера, который может вызвать баги прокрутки при overflow:hidden
                    event.preventDefault();

                    // Находим нужный элемент на странице
                    const targetElement = document.querySelector(href);
                    if (targetElement) {
                        // Прокручиваем к элементу (даже при активном no-scroll на body)
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            }
        });

        // Закрытие при клике вне области меню и кнопки
        document.addEventListener('click', (event) => {
            const isClickInsideMenu = mobileMenu.contains(event.target);
            const isClickOnToggle = menuToggle.contains(event.target);
            const isMenuOpen = mobileMenu.classList.contains('active');

            // Если меню открыто и клик был НЕ по меню и НЕ по кнопке
            if (isMenuOpen && !isClickInsideMenu && !isClickOnToggle) {
                closeMenu();
            }
        });
    });

    // Плавная прокрутка к якорям
    document.addEventListener('DOMContentLoaded', () => {
        // Находим все ссылки, которые ведут к внутренним якорям (начинаются с #)
        const anchorLinks = document.querySelectorAll('a[href^="#"]');

        anchorLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                // Отменяем стандартное мгновенное переключение
                e.preventDefault();

                // Получаем id элемента из атрибута href
                const targetId = this.getAttribute('href');

                // Проверяем, что это не просто пустая ссылка "#"
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    // Плавно прокручиваем к элементу
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start' // Прокрутка до верхнего края элемента
                    });
                }
            });
        });
    });

})();



