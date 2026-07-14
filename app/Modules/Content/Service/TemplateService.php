<?php
declare(strict_types=1);

namespace App\Modules\Content\Service;

use App\Modules\Content\Entity\Page;
use App\Modules\Content\Entity\Widgets\ProductWidget;
use App\Modules\Content\Entity\Widgets\Template;
use App\Modules\Content\Repository\TemplateRepository;
use Illuminate\Http\Request;

class TemplateService
{
    private TemplateRepository $repository;

    public function __construct(TemplateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(Request $request): array
    {
        $type = $request->string('type')->value();
        $template = $request->string('template')->trim()->value();
        $name = $request->string('name')->trim()->value();
        if (!file_exists(Template::Path($type)))
            mkdir(Template::Path($type), 0777, true);
        $file = Template::File($type, $template);
        //Заменяем данные в шаблоне
        //Возможно расширение параметров, для версии 0.2 dummyParamsName = Имя шаблона
        $content = str_replace([
            'dummyParamsName',
        ], [
            $name,
        ],
            file_get_contents(Template::Base($type))
        );

        file_put_contents($file, $content);

        return ['type' => $type, 'template' => $template];
    }


    public function destroy(string $type, string $template)
    {
        $file = Template::File($type, $template);

        $isset = null;
        if ($type == 'widget') {
            $isset = ProductWidget::where('template', $template)->first();
        }
        if ($type == 'page') {
            $isset = Page::where('template', $template)->first();
        }

        if (is_null($isset)) {
            unlink($file);
        } else {
            throw new \DomainException('Шаблон используется. Удалить нельзя');
        }

    }

    public function update(Request $request)
    {
        $content = $request->string('content')->value();
        $type = $request->string('type')->value();
        $template = $request->string('template')->value();
        $file = Template::File($type, $template);

        file_put_contents($file, $content);
    }
}
