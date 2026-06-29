<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Admin menu items for Product module
|--------------------------------------------------------------------------
|
| Register menu items following the format below.
| Replace 'Product' with the plural form (e.g., 'users', 'pages').
|
| Each item requires:
|   - sort:        int (sorting order in sidebar)
|   - icon:        string (Lucide icon name, e.g. 'users', 'settings')
|   - title:       string (display text in sidebar)
|   - route_name:  string (named route, e.g. 'admin.catalog.index')
|   - can:         string (permission gate, e.g. 'staff', 'pages')
|   - vue:         bool (uses Vue/Inertia frontend)
|   - font_awesome: string (Font Awesome class, e.g. 'fa-light fa-users')
|
*/

return [
    '{{ name_plural }}' => [
        'sort'         => 10,
        'icon'         => 'users',
        'title'        => 'Product',
        'route_name'   => 'admin.catalog.product.index',
        'can'          => 'Product',
        'vue'          => true,
        'font_awesome' => 'fa-light fa-users',
    ],
];
