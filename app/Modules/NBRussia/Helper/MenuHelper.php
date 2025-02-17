<?php
declare(strict_types=1);

namespace App\Modules\NBRussia\Helper;

use App\Modules\Page\Entity\Contact;
use App\Modules\Page\Entity\Page;

class MenuHelper
{
    public static function getMenuPages(): array
    {
        $pages = Page::where('published', true)
            ->where('parent_id', null)
            ->where('menu', true)
            ->orderBy('sort')
            ->getModels();
        $add_items = [
        ];

        return array_merge(array_map(function (Page $page) {
            return [
                'name' => $page->name,
                'icon' => '', //TODO Возможно сделать иконку для Page
                'route' => route('shop.page.view', $page->slug),
            ];

        }, $pages), $add_items);

    }

    public static function getMenuContacts(): array
    {
        $contacts = Contact::where('published', true)->orderBy('sort')->getModels();
        return array_map(function (Contact $contact) {
            return [
                'name' => $contact->name,
                'icon' => $contact->icon,
                'color' => $contact->color,
                'url' => $contact->url,
                'data-type' => $contact->type,
            ];

        }, $contacts);
    }

    public static function getFooterMenu(): array
    {
        return [
            'column-1' => [
                'title' => 'Обслуживание клиентов',
                'items' => [
                    [
                        'name' => 'Доставка и Оплата',
                        'icon' => '',
                        'route' => route('shop.page.view', 'delivery'),
                    ],
                    [
                        'name' => 'Обмен и Возврат',
                        'icon' => '',
                        'route' => route('shop.page.view', 'refund'),
                    ],
                    [
                        'name' => 'Потребительская информация',
                        'icon' => '',
                        'route' => route('shop.page.view', 'information'),
                    ],
                    [
                        'name' => 'Как выбрать размер одежды',
                        'icon' => '',
                        'route' => route('shop.page.view', 'size'),
                    ],
                ],
            ],
            'column-2' => [
                'title' => 'Информация',
                'items' => [//TODO сделать аналог self::getMenuPages(),
                    [
                        'name' => 'Отзывы',
                        'icon' => '',
                        'route' => route('shop.page.view', 'review'),
                    ],
                    [
                        'name' => 'Условия использования сайта',
                        'icon' => '',
                        'route' => route('shop.page.view', 'condition'),
                    ],
                    [
                        'name' => 'Политика обработки персональных данных',
                        'icon' => '',
                        'route' => route('shop.page.view', 'political'),
                    ],
                    [
                        'name' => 'Контактная информация',
                        'icon' => '',
                        'route' => route('shop.page.view', 'contacts'),
                    ],
                ],
            ],

        ];
    }

    public static function getCabinetMenu(): array
    {
        return [
            'cabinet' => [
                'name' => 'Личный кабинет',
                'icon' => 'fa-light fa-user-vneck',
                'url' => route('cabinet.view'),
            ],
            'orders' => [
                'name' => 'Мои заказы',
                'icon' => 'fa-sharp fa-light fa-box-open',
                'url' => route('cabinet.order.index'),
            ],
            'wish' => [
                'name' => 'Избранное',
                'icon' => 'fa-light fa-heart',
                'url' => route('cabinet.wish.index'),
            ],
            'cart' => [
                'name' => 'Корзина',
                'icon' => 'fa-light fa-cart-shopping',
                'url' => route('shop.cart.view'),
            ],
            'review' => [
                'name' => 'Мои отзывы',
                'icon' => 'fa-sharp fa-light fa-message-smile',
                'url' => route('cabinet.review.index'),
            ],
            'options' => [
                'name' => 'Настройки',
                'icon' => 'fa-light fa-user-gear',
                'url' => route('cabinet.options.index'),
            ],
            'logout' => [
                'name' => 'Выход',
                'icon' => 'fa-light fa-right-from-bracket',
                'url' => route('logout'),
            ],
        ];
    }
}
