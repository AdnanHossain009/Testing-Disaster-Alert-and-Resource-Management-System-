<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user has language preference in session
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
            app()->setLocale($locale);
        } 
        // Check if user is authenticated and has language preference
        elseif ($request->user() && $request->user()->language) {
            app()->setLocale($request->user()->language);
        }
        // Default to English
        else {
            app()->setLocale('en');
        }

        return $next($request);
    }
}
