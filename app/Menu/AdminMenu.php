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
                'can' => 'user-manager',
            ],
            'clients' => [
                'icon' => 'users',
                'title' => 'Клиенты',
                'route_name' => 'admin.users.index',
                'can' => 'user-manager',
            ],
            'divider',
            'sales' => [
                'icon' => 'coins',
                'title' => 'Продажи',
                'can' => '',
                'sub_menu' => [
                    'order' => [
                        'icon' => 'file-plus-2',
                        'title' => 'Заказы (new)',
                        'route_name' => 'admin.sales.order.index',
                    ],
                    'preorder' => [
                        'icon' => 'file-scan',
                        'title' => 'Предзаказы',
                        'route_name' => 'admin.sales.preorder.index',
                    ],
                    'executed' => [
                        'icon' => 'file-check-2',
                        'title' => 'Заказы (исп.)',
                        'route_name' => 'admin.sales.executed.index',
                    ],
                    'reserve' => [
                        'icon' => 'baggage-claim',
                        'title' => 'Резерв',
                        'route_name' => 'admin.sales.reserve.index',
                    ],
                    'cart' => [
                        'icon' => 'shopping-cart',
                        'title' => 'Корзина',
                        'route_name' => 'admin.sales.cart.index',
                    ],
                ],
            ],
            'delivery' => [
                'icon' => 'truck',
                'title' => 'Доставка',
                'can' => '',
                'sub_menu' => [
                    'storage' => [
                        'icon' => 'warehouse',
                        'title' => 'Самовывоз',
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
                        'title' => 'Доставка ТК',
                        'route_name' => 'admin.delivery.region',
                        'action' => true,
                    ],
                ],

            ],
            'divider',
            'products' => [
                'icon' => 'store',
                'title' => 'Магазин',
                'can' => 'commodity',
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
                ],
            ],
            'discount' => [
                'icon' => 'badge-percent',
                'title' => 'Скидки',
                'can' => 'commodity',
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
            'commodity' => [
                'icon' => 'user',
                'title' => 'Товаровед',
                'can' => 'commodity',
                'sub_menu' => [
                    'arrival' => [
                        'icon' => 'package-search',
                        'title' => 'Поступление',
                        'route_name' => 'admin.home',
                    ],
                    'suppliers' => [
                        'icon' => 'package-search',
                        'title' => 'Поставщики',
                        'route_name' => 'admin.home',
                    ],
                ],
            ],
            'company' => [
                'icon' => 'building',
                'title' => 'Организация',
                'route_name' => 'admin.home',
                'can' => '',
            ],
            'providers' => [
                'icon' => 'building',
                'title' => 'Поставщики',
                'route_name' => 'admin.home',
                'can' => '',
            ],
            'dictionary' => [
                'icon' => 'building',
                'title' => 'Справочники',
                'can' => 'user-manager',
                'sub_menu' => [
                    'regions' => [
                        'icon' => 'package-search',
                        'title' => 'Регионы',
                        'route_name' => 'admin.home',
                    ],
                ],
            ],
            'options' => [
                'icon' => 'settings',
                'title' => 'Настройки',
                'can' => 'user-manager',
                'sub_menu' => [
                    'shop' => [
                        'icon' => 'store',
                        'title' => 'Интернет магазина',
                        'route_name' => 'admin.settings.shop',//admin.options.shop.index
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
