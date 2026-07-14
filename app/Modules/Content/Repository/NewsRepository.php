<?php

namespace App\Modules\Content\Repository;


use App\Modules\Content\Entity\News;

class NewsRepository
{

    public function getIndex(\Illuminate\Http\Request $request)
    {
        $query = News::orderByDesc('created_at');
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(News $news) => $this->NewsToArray($news));
    }

    private function NewsToArray(News $news): array
    {
        return array_merge($news->toArray(), [
            'image' => $news->getImage(),
        ]);
    }
}
