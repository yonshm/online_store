<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLanguage($locale)
    {
        $supportedLocales = ['en', 'fr', 'ar'];
        
        if (in_array($locale, $supportedLocales)) {
            Session::put('locale', $locale);
        }
        
        return redirect()->back();
    }
} 