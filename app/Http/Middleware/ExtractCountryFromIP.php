<?php

namespace App\Http\Middleware;

use App\HelperMethods\HelperMethod;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use GeoIp2\Database\Reader;

class ExtractCountryFromIP
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $helper = new HelperMethod();
        $reader = new Reader(storage_path('app/geoip/GeoLite2-Country.mmdb'));
        $ipAddress = $request->ip();

        try {
            $country = $reader->country($ipAddress);
            $countryCode = $country->country->isoCode;
            $request->merge(['country_code' => $countryCode]);
        } catch (\Exception $e) {
            return $helper->getErrorResponse($e);
        }

        return $next($request);
    }
}
