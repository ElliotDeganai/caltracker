<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ShareAppSettings
{
    public function handle(Request $request, Closure $next)
    {
        $logoPath = AppSetting::get('logo_path');

        Inertia::share('appSettings', [
            'app_name' => AppSetting::get('app_name', config('app.name')),
            'browser_title' => AppSetting::get('browser_title', config('app.name')),
            'logo_url' => $logoPath ? Storage::url($logoPath) : null,
            'page_slugs' => AppSetting::pageSlugs(),
        ]);

        $user = $request->user();

        Inertia::share('auth.permissions', $user ? $user->getAllPermissions()->pluck('name') : []);
        Inertia::share('auth.roles', $user ? $user->getRoleNames() : []);

        if ($user && $user->can('manage-users')) {
            Inertia::share('admin.users', User::select('id', 'name', 'email')->orderBy('name')->get());

            $viewingId = session('viewing_user_id');
            Inertia::share('admin.viewingUser', $viewingId ? User::find($viewingId) : null);
        }

        return $next($request);
    }
}
