<?php

declare(strict_types=1);

namespace App\Modules\Shared\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\PhotoType;
use Illuminate\Http\UploadedFile;

final class PhotoEntity
{
    public ?int $id = null {
        get => $this->id;
        set => $this->id = $value;
    }

    public int $imageableId {
        get => $this->imageableId;
        set => $this->imageableId = $value;
    }

    public string $imageableType {
        get => $this->imageableType;
        set => $this->imageableType = $value;
    }

    public string $modelType {
        get => $this->modelType;
        set => $this->modelType = $value;
    }

    public string $file {
        get => $this->file;
        set => $this->file = $value;
    }

    public string $alt = '' {
        get => $this->alt;
        set => $this->alt = $value;
    }

    public string $slug = '' {
        get => $this->slug;
        set => $this->slug = $value;
    }

    public string $title = '' {
        get => $this->title;
        set => $this->title = $value;
    }

    public string $description = '' {
        get => $this->description;
        set => $this->description = $value;
    }

    public int $sort = 0 {
        get => $this->sort;
        set => $this->sort = $value;
    }

    public PhotoType $type {
        get => $this->type;
        set => $this->type = $value;
    }

    public bool $thumb = true {
        get => $this->thumb;
        set => $this->thumb = $value;
    }

    public ?string $uploadUrl = null {
        get => $this->uploadUrl;
        set => $this->uploadUrl = $value;
    }

    public function __construct(
        int $imageableId,
        string $imageableType,
        string $modelType,
        string $file,
        PhotoType $type,
    ) {
        $this->imageableId = $imageableId;
        $this->imageableType = $imageableType;
        $this->modelType = $modelType;
        $this->file = $file;
        $this->type = $type;
    }
}
