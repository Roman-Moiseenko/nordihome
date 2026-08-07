<?php

namespace App\Modules\Shop\Infrastructure\Persistence\Query;

use App\Modules\Shop\Application\DTOs\Menu\ContactData;
use Illuminate\Support\Facades\DB;

class ContactQueryRepository
{
    /** @return ContactData[] */
    public function getPublishedContacts(): array
    {
        $rows = DB::table('contacts')
            ->where('published', true)
            ->orderBy('sort')
            ->get();

        return $rows->map(fn($row) => new ContactData(
            name: $row->name,
            icon: $row->icon,
            color: $row->color,
            url: $row->url,
            type: (int)$row->type,
            slug: $row->slug ?? '',
            svg: $row->svg,
        ))->all();
    }
}
