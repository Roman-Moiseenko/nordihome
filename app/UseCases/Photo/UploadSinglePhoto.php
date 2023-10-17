<?php
declare(strict_types=1);

namespace App\UseCases\Photo;

use Illuminate\Http\UploadedFile;

class UploadSinglePhoto
{

    public function savePhoto(UploadedFile $file, PhotoSingle $object, string $callback = 'setPhoto'): void
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
            $object->$callback('/' . $path . $file->getClientOriginalName());

            $object->save();
        } catch (\Throwable $e) {
            flash($e->getMessage(), 'danger');
        }
    }
}
