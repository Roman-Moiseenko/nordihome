<?php
declare(strict_types=1);

namespace App\Entity;

class Picture
{
    private bool $imageDefault = false;
    private string $pathImage = '';
    private string $tagIcon = '';
    private string $description = '';

    public string $pathToObject = '/';

    public static function create(string $pathImage, string $tagIcon, string $description, bool $imageDefault = false): self
    {
        $picture = new static();
        $picture->pathImage = $pathImage;
        $picture->tagIcon = $tagIcon;
        $picture->description = $description;
        $picture->imageDefault = $imageDefault;

        return $picture;
    }

    public function getHTML(string $class = ''): string
    {
        if (empty($this->tagIcon) && empty($this->pathImage)) return '';
        if (empty($this->tagIcon)) return $this->getImageHTML($class);
        if (empty($this->pathImage)) return $this->getIconHTML($class);
        if ($this->imageDefault) {
            return $this->getImageHTML($class);
        } else {
            return $this->getIconHTML($class);
        }
    }

    public function getIconHTML(string $class = ''): string
    {
        $class = empty($class) ? '' : (' ' . $class);
        return '<i class="' . $this->tagIcon . $class . '" area-label="' . $this->description .'"></i>';
    }

    public function getImageHTML(string $class = ''): string
    {
        return '<img src="'. $this->pathImage . '" alt="' . $this->description .'" ' . (empty($class) ? '' : 'class="'. $class .'"') . '>';
    }

    public function toSave(): string
    {
        return json_encode([
            'pathImage' => $this->pathImage,
            'tagIcon' => $this->tagIcon,
            'description' => $this->description,
            'imageDefault' => $this->imageDefault
        ]);
    }

    public static function load(string $json): self
    {
        $array = json_decode($json, true);
        return self::create(
            $array['pathImage'],
            $array['tagIcon'],
            $array['description'],
            $array['imageDefault']
        );
    }
}
