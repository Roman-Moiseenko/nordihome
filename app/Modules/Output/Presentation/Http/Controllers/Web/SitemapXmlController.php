<?php
declare(strict_types=1);

namespace App\Modules\Output\Presentation\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Output\Application\Queries\Sitemap\GetSitemapQuery;

class SitemapXmlController extends Controller
{
    public function __construct(
        private readonly GetSitemapQuery $sitemapQuery,
    ) {}

    public function index()
    {
        $pages = $this->sitemapQuery->execute();
        $content = view('output.sitemap', compact('pages'))->render();
        ob_end_clean();
        return response($content)->header('Content-Type','text/xml');
    }
}
