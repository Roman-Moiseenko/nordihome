<?php
declare(strict_types=1);

namespace App\UseCases\Uploads;

use Illuminate\Http\UploadedFile;

class UploadService
{

    public function singleReplace(UploadedFile $file, UploadsDirectory $object): string
    {
        try {
            $path = $object->getUploadsDirectory();
            if (!file_exists(public_path() . '/' . $path)) {
                mkdir(public_path() . '/' . $path, 0777, true);
            }
            $file->move($path, $file->getClientOriginalName());
            if (!empty($object->photo)) {
                unlink(public_path() . $object->photo);
            }
            return '/' . $path . $file->getClientOriginalName();
        } catch (\Throwable $e) {
            flash($e->getMessage(), 'danger');
        }
        return '';
    }

    public function removeFile($path): void
    {
        if (!empty($path)) {
            $path = ltrim($path, '\\');
            $path = ltrim($path, '/');
            unlink(public_path() . '/' . $path);
        }
    }
}
