<?php

namespace App\Modules\Base\Entity;

use Illuminate\Http\Request;

class Meta
{
    public string $title;
    public string $description;
    public string $keywords;

    public function toArray(): array
    {
        return [
            'title' => $this->title ?? '',
            'description' => $this->description ?? '',
            'keywords' => $this->keywords ?? '',
        ];
    }

    public static function fromArray(string|null $json): Meta
    {
        $data = json_decode($json, true);
        if (is_null($json) || is_null($data)) return new Meta();
        $meta = new Meta();
        $meta->title = $data['title'] ?? '';
        $meta->description = $data['description'] ?? '';
        $meta->keywords = $data['keywords'] ?? '';

        return $meta;
    }
    public function fromRequest(Request $request): void
    {
        $this->title = $request->string('meta_title')->trim()->value();
        $this->description = $request->string('meta_description')->trim()->value();
    }
}
