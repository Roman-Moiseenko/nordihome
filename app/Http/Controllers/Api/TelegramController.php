<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    public function get(Request $request)
    {

        return response()->json($request);
    }
}
