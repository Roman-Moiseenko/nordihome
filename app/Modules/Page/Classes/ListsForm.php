<?php

namespace App\Modules\Page\Classes;

class ListsForm
{
    /**@var ListForm[] $list */
    public array $list = [];

    public function toArray(): mixed
    {
        return $this;
    }


    public static function fromArray(string|null $json): self
    {
        $data = json_decode($json, true);
        if (is_null($json) || is_null($data)) return new ListsForm();
        $lists = new ListsForm();

        if (isset($data['list'])) {
            $array = $data['list'];
            foreach ($array as $list) {
                $lists->add($list);
            }
        }

        return $lists;
    }

    public function add(array $list): void
    {
        $_list = ListForm::create($list['slug']);
        foreach ($list['items'] as $item) {
            $_list->add($item);
        }
        $this->list[] = $_list;

    }

    public function get(string $slug): array
    {
        foreach ($this->list as $list) {
            if ($list->slug == $slug) return $list->items;
        }
        return [];
    }
}
