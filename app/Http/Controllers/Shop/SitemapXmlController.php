<?php
declare(strict_types=1);

namespace App\Http\Controllers\Shop;

use Illuminate\Routing\Controller;

class SitemapXmlController extends Controller
{

    public function index()
    {
        //TODO Формируем массив данных формата
        $pages[] = [
            'url' => '/',
            'date' => now(),
            'changefreq' => 'weekly'
        ];

        $content = view('shop.sitemap', compact('pages'))->render();
        ob_end_clean();
        return response($content)->header('Content-Type','text/xml');
    }

}
