<?php
declare(strict_types=1);

namespace App\UseCases\Photo;

interface PhotoSingle
{
    public function getUploadsDirectory(): string;
    public function setPhoto(string $file): void;
}
