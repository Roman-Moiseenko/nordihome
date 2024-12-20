<?php
declare(strict_types=1);

namespace App\Modules\Base\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function report(Request $request): BinaryFileResponse|JsonResponse
    {
        try {
            $class = $request->get('class');
            $method = $request->get('method');
            $id = $request->integer('id');
            $file = app()->make($class)->$method($id);

            $headers = [
                'filename' => basename($file),
            ];
            ob_end_clean();
            ob_start();
            return response()->file($file, $headers);
        } catch (\Throwable $e) {
            return response()->json(['error' => [$e->getMessage(), $e->getFile(), $e->getLine()]]);
        }
    }

    public function send(Request $request)
    {
        $class = $request->get('class');
        $method = $request->get('method');
        $id = $request->integer('id');
        $file = (new $class)->$method($id);

        //TODO Отправка служебного письма с файлом $file
    }
}
