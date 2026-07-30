<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;

class HandleSlugRedirects
{
    /**
     * Si l'URL demandée correspond à un ANCIEN slug de page (avant renommage),
     * redirige en 301 vers l'URL actuelle correspondante.
     */
    public function handle(Request $request, Closure $next)
    {
        $segment = trim($request->path(), '/');

        $redirectsRaw = AppSetting::get('slug_redirects');
        $redirects = $redirectsRaw ? json_decode($redirectsRaw, true) : [];

        if (isset($redirects[$segment])) {
            $page = $redirects[$segment];
            $newSlug = AppSetting::slug($page);

            if ($newSlug !== $segment) {
                return redirect('/' . $newSlug, 301);
            }
        }

        return $next($request);
    }
}
