<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    /**
     * Available locales for the application.
     *
     * @var array
     */
    protected $availableLocales = ['en', 'fr', 'ar', 'pt', 'es', 'sw'];

    /**
     * Switch the application language.
     */
    public function switch(Request $request, string $locale)
    {
        if (! in_array($locale, $this->availableLocales, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language code.',
                ], 400);
            }

            return redirect()->back()->with('error', 'Invalid language code.');
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);
        $this->queuePreferredLocaleCookie($locale);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'message' => 'Language changed successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Language changed to ' . $this->getLanguageName($locale) . '.');
    }

    protected function getLanguageName(string $locale): string
    {
        $languages = [
            'en' => 'English',
            'fr' => 'French',
            'ar' => 'Arabic',
            'pt' => 'Portuguese',
            'es' => 'Spanish',
            'sw' => 'Kiswahili',
        ];

        return $languages[$locale] ?? $locale;
    }

    protected function queuePreferredLocaleCookie(string $locale): void
    {
        Cookie::queue(cookie()->make(
            'preferred_locale',
            $locale,
            43200,
            config('session.path', '/'),
            config('session.domain'),
            (bool) config('session.secure', false),
            true,
            false,
            config('session.same_site', 'lax')
        ));
    }

    public function current()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'name' => $this->getLanguageName(App::getLocale()),
        ]);
    }

    public function available()
    {
        $locales = [];

        foreach ($this->availableLocales as $locale) {
            $locales[] = [
                'code' => $locale,
                'name' => $this->getLanguageName($locale),
            ];
        }

        return response()->json([
            'locales' => $locales,
        ]);
    }
}
