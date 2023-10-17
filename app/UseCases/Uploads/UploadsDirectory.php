<?php
declare(strict_types=1);

namespace App\UseCases\Uploads;

interface UploadsDirectory
{
    public function getUploadsDirectory(): string;
}
