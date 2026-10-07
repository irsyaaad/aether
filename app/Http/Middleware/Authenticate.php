<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            return null;
        }

        $enabled = (bool) config('services.persada.redirect_to_hris_on_expire', false);

        if (!$enabled) {
            return route('login');
        }

        $hrisUrl = rtrim((string) config('services.persada.hris_url', 'http://hris.persada.test'), '/');

        return $hrisUrl !== '' ? $hrisUrl : route('login');
    }
}
