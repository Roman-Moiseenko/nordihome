<?php
declare(strict_types=1);

namespace App\Modules\Content\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Content\Repository\TemplateRepository;
use App\Modules\Content\Service\TemplateService;

class TemplateController extends Controller
{
    private TemplateService $service;


    public function __construct(
        TemplateService            $service,
        private TemplateRepository $repository,
    )
    {
        $this->service = $service;

    }

    public function templates()
    {
        //TODO список всех шаблонов по папкам ['floder' => [...],]
    }


}
