<?php
declare(strict_types=1);

namespace App\Modules\Page\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Page\Repository\TemplateRepository;
use App\Modules\Page\Service\TemplateService;

class TemplateController extends Controller
{
    private TemplateService $service;
    private TemplateRepository $repository;

    public function __construct(TemplateService $service, TemplateRepository $repository)
    {
        $this->middleware(['auth:admin', 'can:options']);
        $this->service = $service;
        $this->repository = $repository;
    }

    //TODO *****
}
