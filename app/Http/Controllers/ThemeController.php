<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Switch application active theme.
     *
     * @param string $theme
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchTheme($theme)
    {
        $allowedThemes = ['metronic', 'default'];

        if (in_array($theme, $allowedThemes, true)) {
            session(['app_theme' => $theme]);
        }

        return redirect()->back()->with('success', 'Tema berhasil diubah ke ' . ucfirst($theme));
    }
}
