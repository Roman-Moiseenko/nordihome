<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/file-upload',
        '/admin/staff/photo/*',
        '/admin/accounting/bank/upload'
        /*'/catalog/search',
        '/cart_post/cart',
        '/cabinet/wish/get'*/
        //'/admin/product/attribute/get_by_categories',
    ];
}
