<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Entity\LogActivity;
use Closure;
use \Illuminate\Http\Request;

class AdminActivityLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user_id = $request->user()->id;
        $action = $request->route()->getName();
        if (!empty($request->all())) {
            LogActivity::register(
                $user_id,
                $action,
                $request->url(),
                $request->all()
            );
        }
        return $next($request);
    }
}
