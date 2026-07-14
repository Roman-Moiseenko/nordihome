<?php
declare(strict_types=1);

namespace App\Modules\Content\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Content\Repository\TemplateRepository;
use App\Modules\Content\Service\TemplateService;

class TemplateController extends Controller
{
    private TemplateService $service;
    private TemplateRepository $repository;

    public function __construct(TemplateService $service, TemplateRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    //TODO *****
}
