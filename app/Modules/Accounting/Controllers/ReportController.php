<?php
declare(strict_types=1);

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function report(Request $request): BinaryFileResponse
    {
        dd($request->all());
        $class = $request->get('class');
        $id = $request->integer('id');
        $file = $class($id);
        return response()->file($file);
    }
}
