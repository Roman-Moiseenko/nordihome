<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Modules\Analytics\Entity\LoggerActivity;
use Closure;
use Illuminate\Http\Request;

class AdminActivityLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $user_id = $request->user()->id;
        $action = $request->route()->getName();
        if (!empty($request->all())) {
            LoggerActivity::register(
                $user_id,
                $action,
                $request->url(),
                $request->all()
            );
        }
        return $next($request);
    }
}
