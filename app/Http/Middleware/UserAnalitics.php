<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserAnalitics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       /* $ip = Request::ip();
        $referer = Request::server('HTTP_REFERER');
        $url = $request->url();
        https://github.com/stevebauman/location
        Stevebauman\Location\Facades\Location
        $location = Location::get($ip);
        $country = $location->countryName ?? null;
        $city = $location->cityName ?? null;

        Visit::create([
            'ip_address' => $ip,
            'referer' => $referer,
            'url' => $url,
            'country' => $country,
            'city' => $city,
            'visited_at' => now(),
        ]);
*/
        return $next($request);
    }
}
