<?php
declare(strict_types=1);

namespace App\Menu;

class AdminMenu
{
    public static function menu(): array
    {
        return [
            'staff' => [
                'icon' => 'contact',
                'title' => 'Сотрудники',
                'route_name' => 'admin.staff.index',
                'can' => 'staff',
            ],
            'worker' => [
                'icon' => 'anvil',
                'title' => 'Рабочие',
                'route_name' => 'admin.worker.index',
                'can' => 'staff',
            ],

            'clients' => [
                'icon' => 'users',
                'title' => 'Клиенты',
                'can' => 'user',
                'sub_menu' => [
                    'users' => [
                        'icon' => 'user-search',
                        'title' => 'Список',
                        'route_name' => 'admin.user.index',
                    ],
                    'subscriptions' => [
                        'icon' => 'bell-ring',
                        'title' => 'Подписки',
                        'route_name' => 'admin.user.subscription.index',
                    ],
                    'cart' => [
                        'icon' => 'shopping-cart',
                        'title' => 'Корзина',
                        'route_name' => 'admin.user.cart.index',
                        'can' => 'order',
                    ],
                    'wish' => [
                        'icon' => 'heart',
                        'title' => 'Избранное',
                        'route_name' => 'admin.user.wish.index',
                        'can' => 'order',
                    ],
                ],
            ],
            'divider',
            'orders' => [
                'icon' => 'coins',
                'title' => 'Продажи',
                'can' => ['order','payment', 'refund'],
                'sub_menu' => [
                    'order' => [
                        'icon' => 'file-plus-2',
                        'title' => 'Заказы',
                        'route_name' => 'admin.order.index',
                        'can' => 'order',
                    ],
                    'payment' => [
                        'icon' => 'credit-card',
                        'title' => 'Платежи',
                        'route_name' => 'admin.order.payment.index',
                        'can' => 'payment',
                    ],
                    'refund' => [
                        'icon' => 'refresh-ccw',
                        'title' => 'Возвраты',
                        'route_name' => 'admin.order.refund.index',
                        'can' => 'refund',
                    ],
                    'reserve' => [
                        'icon' => 'baggage-claim',
                        'title' => 'Резерв',
                        'route_name' => 'admin.order.reserve.index',
                        'can' => 'order',
                    ],
                ],
            ],
            'delivery' => [
                'icon' => 'plane',
                'title' => 'Доставка',
                'can' => ['delivery'],
                'sub_menu' => [
                    'truck' => [
                        'icon' => 'truck',
                        'title' => 'Транспорт',
                        'route_name' => 'admin.delivery.truck.index',
                    ],
                    'storage' => [
                        'icon' => 'warehouse',
                        'title' => 'Выдача со склада',
                        'route_name' => 'admin.delivery.storage',
                        'action' => true,
                    ],
                    'local' => [
                        'icon' => 'map-pin',
                        'title' => 'Доставка по региону',
                        'route_name' => 'admin.delivery.local',
                        'action' => true,
                    ],
                    'region' => [
                        'icon' => 'map',
                        'title' => 'Доставка по РФ',
                        'route_name' => 'admin.delivery.region',
                        'action' => true,
                    ],
                    'calendar' => [
                        'icon' => 'calendar-days',
                        'title' => 'Календарь доставок',
                        'route_name' => 'admin.delivery.calendar.index',
                        //'action' => true,
                    ],
                    'schedule' => [
                        'icon' => 'calendar-check',
                        'title' => 'График доставок',
                        'route_name' => 'admin.delivery.calendar.schedule',
                        'action' => true,
                    ],
                ],

            ],
            'divider',
            'shop' => [
                'icon' => 'store',
                'title' => 'Магазин',
                'can' => 'product',
                'sub_menu' => [
                    'product' => [
                        'icon' => 'package-open',
                        'title' => 'Все Товары',
                        'route_name' => 'admin.product.index',
                    ],
                    'category' => [
                        'icon' => 'file-box',
                        'title' => 'Категории',
                        'route_name' => 'admin.product.category.index',
                    ],
                    'modification' => [
                        'icon' => 'file-cog',
                        'title' => 'Модификации',
                        'route_name' => 'admin.product.modification.index', // 'admin.product.tag.index'
                    ],
                    'option' => [
                        'icon' => 'package-plus',
                        'title' => 'Опции',
                        'route_name' => 'admin.home', // 'admin.product.tag.index'
                    ],
                    'equivalent' => [
                        'icon' => 'package-check',
                        'title' => 'Аналоги',
                        'route_name' => 'admin.product.equivalent.index',
                    ],
                    'group' => [
                        'icon' => 'boxes',
                        'title' => 'Группы товаров',
                        'route_name' => 'admin.product.group.index',
                    ],
                    'attribute' => [
                        'icon' => 'blocks',
                        'title' => 'Атрибуты',
                        'route_name' => 'admin.product.attribute.index',
                    ],
                    'tags' => [
                        'icon' => 'tags',
                        'title' => 'Метки',
                        'route_name' => 'admin.product.tag.index',
                    ],
                    'brands' => [
                        'icon' => 'pocket',
                        'title' => 'Бренды',
                        'route_name' => 'admin.product.brand.index',
                    ],
                    'series' => [
                        'icon' => 'component',
                        'title' => 'Серии',
                        'route_name' => 'admin.product.series.index',
                    ],
                    'priority' => [
                        'icon' => 'flag-triangle-right  ',
                        'title' => 'Приоритет',
                        'route_name' => 'admin.product.priority.index',
                    ],
                    'parser' => [
                        'icon' => 'package-search',
                        'title' => 'Парсер',
                        'route_name' => 'admin.product.parser.index',
                    ],
                ],
            ],
            'discount' => [
                'icon' => 'badge-percent',
                'title' => 'Скидки',
                'can' => 'discount',
                'sub_menu' => [
                    'promotion' => [
                        'icon' => 'percent',
                        'title' => 'Акции',
                        'route_name' => 'admin.discount.promotion.index',
                    ],
                    'coupon' => [
                        'icon' => 'piggy-bank',
                        'title' => 'Купоны скидочные',
                        'route_name' => 'admin.home',
                    ],
                    'discount' => [
                        'icon' => 'percent-diamond',
                        'title' => 'Скидки',
                        'route_name' => 'admin.discount.discount.index',
                    ],
                    'bonus' => [
                        'icon' => 'badge-dollar-sign',
                        'title' => 'Бонусные продажи',
                        'route_name' => 'admin.discount.discount.index',
                    ],
//
                ],
            ],
            'divider',
            'accounting' => [
                'icon' => 'database',
                'title' => 'Товарный учет',
                'can' => 'accounting',
                'sub_menu' => [
                    'arrival' => [
                        'icon' => 'folder-input',
                        'title' => 'Поступление',
                        'route_name' => 'admin.accounting.arrival.index',
                    ],
                    'movement' => [
                        'icon' => 'folder-sync',
                        'title' => 'Перемещение товара',
                        'route_name' => 'admin.accounting.movement.index',
                    ],
                    'departure' => [
                        'icon' => 'folder-output',
                        'title' => 'Списание товара',
                        'route_name' => 'admin.accounting.departure.index',
                    ],
                    'supply' => [
                        'icon' => 'folder-pen',
                        'title' => 'Заказы поставщикам',
                        'route_name' => 'admin.accounting.supply.index',
                    ],
                    'pricing' => [
                        'icon' => 'badge-russian-ruble',
                        'title' => 'Ценообразование',
                        'route_name' => 'admin.accounting.pricing.index',
                    ],
                    'distributors' => [
                        'icon' => 'building',
                        'title' => 'Поставщики',
                        'route_name' => 'admin.accounting.distributor.index',
                    ],
                    'storages' => [
                        'icon' => 'warehouse',
                        'title' => 'Хранилища',
                        'route_name' => 'admin.accounting.storage.index',
                    ],
                    'currency' => [
                        'icon' => 'candlestick-chart',
                        'title' => 'Курс валют',
                        'route_name' => 'admin.accounting.currency.index',
                    ],
                    'organization' => [
                        'icon' => 'landmark',
                        'title' => 'Организации',
                        'route_name' => 'admin.accounting.organization.index',
                        'can' => '',
                    ],
                ],
            ],
            'divider',
            'task' => [
                'icon' => 'clipboard-check',
                'title' => 'Задачи',
                'can' => '',
                'sub_menu' => [
                    'notification' => [
                        'icon' => 'bell-ring',
                        'title' => 'Уведомления',
                        'route_name' => 'admin.staff.notification',
                        'action' => true,
                    ],
                    'mail' => [
                        'icon' => 'mail',
                        'title' => 'Почта',
                        'route_name' => 'admin.home',
                    ],
                ],
            ],
            'feedback' => [
                'icon' => 'messages-square',
                'title' => 'Обратная связь',
                'can' => ['feedback', 'review'],
                'sub_menu' => [
                    'review' => [
                        'icon' => 'message-square-warning',
                        'title' => 'Отзывы',
                        'route_name' => 'admin.feedback.review.index',
                        'can' => 'review',
                    ],
                    'mail' => [
                        'icon' => 'mail',
                        'title' => 'Жалобы клиентов',
                        'route_name' => 'admin.home',
                        'can' => 'message-circle-warning',
                    ],
                ],
            ],
            'divider',
            'page' => [
                'icon' => 'monitor',
                'title' => 'Фронтенд',
                'can' => 'options',
                'sub_menu' => [
                    'widgets' => [
                        'icon' => 'film',
                        'title' => 'Виджеты',
                        'route_name' => 'admin.page.widget.index',
                        ],
                    'pages' => [
                        'icon' => 'files',
                        'title' => 'Страницы',
                        'route_name' => 'admin.page.page.index',
                    ],
                    'maps' => [
                        'icon' => 'map-pinned',
                        'title' => 'Карты',
                        'route_name' => 'admin.home',
                    ],
                    'contacts' => [
                        'icon' => 'contact',
                        'title' => 'Контакты',
                        'route_name' => 'admin.page.contact.index',
                    ],
                    'banners' => [
                        'icon' => 'book-image', //book-image  gallery-horizontal-end
                        'title' => 'Баннеры',
                        'route_name' => 'admin.home',
                    ],

                ],
            ],
            'options' => [
                'icon' => 'settings',
                'title' => 'Настройки',
                'can' => 'admin-panel',
                'sub_menu' => [
                    'shop' => [
                        'icon' => 'store',
                        'title' => 'Интернет магазина',
                        'route_name' => 'admin.settings.shop',
                    ],
                    'admin-panel' => [
                        'icon' => 'package-search',
                        'title' => 'Админка',
                        'route_name' => 'admin.home',
                    ],
                    'delivery' => [
                        'icon' => 'truck',
                        'title' => 'Доставка',
                        'route_name' => 'admin.home',
                    ],
                ],
            ],

            'analytics' => [
                'icon' => 'scroll-text',
                'title' => 'Логгеры',
                'can' => 'admin-panel',
                'sub_menu' => [
                    'shop' => [
                        'icon' => 'users',
                        'title' => 'Сотрудников',
                        'route_name' => 'admin.analytics.activity.index',
                    ],
                    'admin-panel' => [
                        'icon' => 'timer-reset',
                        'title' => 'По расписанию',
                        'route_name' => 'admin.analytics.cron.index',
                    ],

                ],
            ],
            /* Образцы */
            /*  'dashboard' => [
                  'icon' => 'home',
                  'title' => 'Dashboard',
                  'sub_menu' => [
                      'dashboard-overview-1' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu',
                          ],
                          'title' => 'Overview 1'
                      ],
                      'dashboard-overview-2' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu',
                          ],
                          'title' => 'Overview 2'
                      ],
                      'dashboard-overview-3' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu',
                          ],
                          'title' => 'Overview 3'
                      ],
                      'dashboard-overview-4' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu',
                          ],
                          'title' => 'Overview 4'
                      ]
                  ]
              ],
              'menu-layout' => [
                  'icon' => 'box',
                  'title' => 'Menu Layout',
                  'sub_menu' => [
                      'side-menu' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Side Menu'
                      ],
                      'simple-menu' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'simple-menu'
                          ],
                          'title' => 'Simple Menu'
                      ],
                      'top-menu' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'top-menu'
                          ],
                          'title' => 'Top Menu'
                      ]
                  ]
              ],
              'e-commerce' => [
                  'icon' => 'shopping-bag',
                  'title' => 'E-Commerce',
                  'sub_menu' => [
                      'categories' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Categories'
                      ],
                      'add-product' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Add Product',
                      ],
                      'products' => [
                          'icon' => 'activity',
                          'title' => 'Products',
                          'sub_menu' => [
                              'product-list' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Product List'
                              ],
                              'product-grid' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Product Grid'
                              ]
                          ]
                      ],
                      'transactions' => [
                          'icon' => 'activity',
                          'title' => 'Transactions',
                          'sub_menu' => [
                              'transaction-list' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Transaction List'
                              ],
                              'transaction-detail' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Transaction Detail'
                              ]
                          ]
                      ],
                      'sellers' => [
                          'icon' => 'activity',
                          'title' => 'Sellers',
                          'sub_menu' => [
                              'seller-list' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Seller List'
                              ],
                              'seller-detail' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Seller Detail'
                              ]
                          ]
                      ],
                      'reviews' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Reviews'
                      ],
                  ]
              ],
              'inbox' => [
                  'icon' => 'inbox',
                  'route_name' => 'home',
                  'params' => [
                      'layout' => 'side-menu'
                  ],
                  'title' => 'Inbox'
              ],
              'file-manager' => [
                  'icon' => 'hard-drive',
                  'route_name' => 'home',
                  'params' => [
                      'layout' => 'side-menu'
                  ],
                  'title' => 'File Manager'
              ],
              'point-of-sale' => [
                  'icon' => 'credit-card',
                  'route_name' => 'home',
                  'params' => [
                      'layout' => 'side-menu'
                  ],
                  'title' => 'Point of Sale'
              ],
              'chat' => [
                  'icon' => 'message-square',
                  'route_name' => 'home',
                  'params' => [
                      'layout' => 'side-menu'
                  ],
                  'title' => 'Chat'
              ],
              'post' => [
                  'icon' => 'file-text',
                  'route_name' => 'home',
                  'params' => [
                      'layout' => 'side-menu'
                  ],
                  'title' => 'Post'
              ],
              'calendar' => [
                  'icon' => 'calendar',
                  'route_name' => 'home',
                  'params' => [
                      'layout' => 'side-menu'
                  ],
                  'title' => 'Calendar'
              ],
              'divider',
              'crud' => [
                  'icon' => 'edit',
                  'title' => 'Crud',
                  'sub_menu' => [
                      'crud-data-list' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Data List'
                      ],
                      'crud-form' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Form'
                      ]
                  ]
              ],
              'users' => [
                  'icon' => 'users',
                  'title' => 'Users',
                  'sub_menu' => [
                      'users-layout-1' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Layout 1'
                      ],
                      'users-layout-2' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Layout 2'
                      ],
                      'users-layout-3' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Layout 3'
                      ]
                  ]
              ],
              'profile' => [
                  'icon' => 'trello',
                  'title' => 'Profile',
                  'sub_menu' => [
                      'profile-overview-1' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Overview 1'
                      ],
                      'profile-overview-2' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Overview 2'
                      ],
                      'profile-overview-3' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Overview 3'
                      ]
                  ]
              ],
              'pages' => [
                  'icon' => 'layout',
                  'title' => 'Pages',
                  'sub_menu' => [
                      'wizards' => [
                          'icon' => 'activity',
                          'title' => 'Wizards',
                          'sub_menu' => [
                              'wizard-layout-1' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 1'
                              ],
                              'wizard-layout-2' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 2'
                              ],
                              'wizard-layout-3' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 3'
                              ]
                          ]
                      ],
                      'blog' => [
                          'icon' => 'activity',
                          'title' => 'Blog',
                          'sub_menu' => [
                              'blog-layout-1' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 1'
                              ],
                              'blog-layout-2' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 2'
                              ],
                              'blog-layout-3' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 3'
                              ]
                          ]
                      ],
                      'pricing' => [
                          'icon' => 'activity',
                          'title' => 'Pricing',
                          'sub_menu' => [
                              'pricing-layout-1' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 1'
                              ],
                              'pricing-layout-2' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 2'
                              ]
                          ]
                      ],
                      'invoice' => [
                          'icon' => 'activity',
                          'title' => 'Invoice',
                          'sub_menu' => [
                              'invoice-layout-1' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 1'
                              ],
                              'invoice-layout-2' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 2'
                              ]
                          ]
                      ],
                      'faq' => [
                          'icon' => 'activity',
                          'title' => 'FAQ',
                          'sub_menu' => [
                              'faq-layout-1' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 1'
                              ],
                              'faq-layout-2' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 2'
                              ],
                              'faq-layout-3' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Layout 3'
                              ]
                          ]
                      ],
                      'login' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'base'
                          ],
                          'title' => 'Login'
                      ],
                      'register' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'base'
                          ],
                          'title' => 'Register'
                      ],
                      'error-page' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'base'
                          ],
                          'title' => 'Error Page'
                      ],
                      'update-profile' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Update profile'
                      ],
                      'change-password' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Change Password'
                      ]
                  ]
              ],
              'divider',
              'components' => [
                  'icon' => 'inbox',
                  'title' => 'Components',
                  'sub_menu' => [
                      'grid' => [
                          'icon' => 'activity',
                          'title' => 'Grid',
                          'sub_menu' => [
                              'regular-table' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Regular Table'
                              ],
                              'tabulator' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Tabulator'
                              ]
                          ]
                      ],
                      'overlay' => [
                          'icon' => 'activity',
                          'title' => 'Overlay',
                          'sub_menu' => [
                              'modal' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Modal'
                              ],
                              'slide-over' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Slide Over'
                              ],
                              'notification' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Notification'
                              ],
                          ]
                      ],
                      'tab' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Tab'
                      ],
                      'accordion' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Accordion'
                      ],
                      'button' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Button'
                      ],
                      'alert' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Alert'
                      ],
                      'progress-bar' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Progress Bar'
                      ],
                      'tooltip' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Tooltip'
                      ],
                      'dropdown' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Dropdown'
                      ],
                      'typography' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Typography'
                      ],
                      'icon' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Icon'
                      ],
                      'loading-icon' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Loading Icon'
                      ]
                  ]
              ],
              'forms' => [
                  'icon' => 'sidebar',
                  'title' => 'Forms',
                  'sub_menu' => [
                      'regular-form' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Regular Form'
                      ],
                      'datepicker' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Datepicker'
                      ],
                      'tom-select' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Tom Select'
                      ],
                      'file-upload' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'File Upload'
                      ],
                      'wysiwyg-editor' => [
                          'icon' => 'activity',
                          'title' => 'Wysiwyg Editor',
                          'sub_menu' => [
                              'wysiwyg-editor-classic' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Classic'
                              ],
                              'wysiwyg-editor-inline' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Inline'
                              ],
                              'wysiwyg-editor-balloon' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Balloon'
                              ],
                              'wysiwyg-editor-balloon-block' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Balloon Block'
                              ],
                              'wysiwyg-editor-document' => [
                                  'icon' => 'zap',
                                  'route_name' => 'home',
                                  'params' => [
                                      'layout' => 'side-menu'
                                  ],
                                  'title' => 'Document'
                              ],
                          ]
                      ],
                      'validation' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Validation'
                      ]
                  ]
              ],
              'widgets' => [
                  'icon' => 'hard-drive',
                  'title' => 'Widgets',
                  'sub_menu' => [
                      'chart' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Chart'
                      ],
                      'slider' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Slider'
                      ],
                      'image-zoom' => [
                          'icon' => 'activity',
                          'route_name' => 'home',
                          'params' => [
                              'layout' => 'side-menu'
                          ],
                          'title' => 'Image Zoom'
                      ]
                  ]
              ] */
        ];
    }
}
