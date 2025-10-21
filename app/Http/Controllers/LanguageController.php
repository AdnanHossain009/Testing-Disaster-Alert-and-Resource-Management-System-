<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch application language
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'bn'])) {
            abort(400, 'Invalid language');
        }

        // Store in session
        $request->session()->put('locale', $locale);

        // If user is authenticated, update their language preference
        if ($request->user()) {
            $request->user()->update(['language' => $locale]);
        }

        // Return back to previous page
        return redirect()->back();
    }
}
