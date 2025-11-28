<?php

namespace App\Modules\Feedback\Classes;


class DataFieldFeedback
{
    public string $slug;
    public string $name;
    public string $value;

    public static function create(string $slug = "", string $name = "", string $value = ""): static
    {
        $data = new static();
        $data->slug = $slug;
        $data->name = $name;
        $data->value = $value;
        return $data;
    }

    public static function fromArray(array $array): array
    {
        $result = [];
        foreach ($array as $item) {
            $result[] = DataFieldFeedback::create(
                $item['slug'] ?? '',
                    $item['name'] ?? '',
                    $item['value'] ?? '',
            );
        }
        return $result;

    }
}
