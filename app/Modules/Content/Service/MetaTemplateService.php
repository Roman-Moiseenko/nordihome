<?php

namespace App\Modules\Content\Service;

use App\Modules\Content\Infrastructure\Models\MetaTemplate;
use Illuminate\Http\Request;

class MetaTemplateService
{

    public function setData(Request $request): void
    {
        $templates = $request->input('templates');
        foreach ($templates as $template) {
            /** @var MetaTemplate $meta */
            $meta = MetaTemplate::find($template['id']);
            $meta->template_title = $template['title'] ?? '';
            $meta->template_description = $template['description'] ?? '';
            $meta->save();
        }
    }



}
