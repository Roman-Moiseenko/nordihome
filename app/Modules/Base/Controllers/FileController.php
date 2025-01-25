<?php
declare(strict_types=1);

namespace App\Modules\Base\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Entity\Organization;
use App\Modules\Base\Entity\FileStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    public function test()
    {
        return view('admin.test');
    }

    public function download(Request $request)
    {
        try {
            /** @var FileStorage $file */
            $file = FileStorage::find($request->integer('id'));
            $path = $file->getUploadFile();
            ob_get_clean();
            return response()->download($path);
        } catch (\Throwable $e) {
            return response()->json($e->getMessage());
        }
    }

    public function remove_file(Request $request): RedirectResponse
    {
        try {
            /** @var FileStorage $file */
            $file = FileStorage::find($request->integer('id'));
            $file->delete();
            return redirect()->back()->with('success', 'Файл удален');
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }
}
