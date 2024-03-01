<?php

namespace App\Http\Middleware;

use App\HelperMethods\HelperMethod;
use App\Types\UserType;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddelware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    private HelperMethod $helper;
    public function __construct()
    {
        $this->helper = new HelperMethod();
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->type !== UserType::ADMIN){
            return $this->helper->requestUnAuthorizedResponse();
        }
        return $next($request);
    }
}
