<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale', 'en');
        
        // Vérifier si la langue est supportée
        $supportedLocales = ['en', 'fr', 'ar'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }
        
        App::setLocale($locale);
        
        return $next($request);
    }
} 