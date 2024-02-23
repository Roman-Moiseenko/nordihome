<?php
declare(strict_types=1);

namespace App\Modules\Product\Service;

use App\Modules\Product\Entity\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagService
{
    public function create(string $name): Tag
    {
        $tag = Tag::register($name);
        return $tag;
    }

    public function rename(Request $request, Tag $tag)
    {
        $tag->name = $request['name'];
        $tag->slug = Str::slug($tag->name);
        $tag->save();
    }

    public function delete(Tag $tag)
    {
        if (!empty($tag->products)) {
            $tag->products()->detach();
        }
        Tag::destroy($tag->id);
    }
}
