<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings', [
            'settings' => [
                'app_name' => AppSetting::get('app_name', config('app.name')),
                'browser_title' => AppSetting::get('browser_title', config('app.name')),
                'logo_url' => AppSetting::get('logo_path') ? Storage::url(AppSetting::get('logo_path')) : null,
                'page_slugs' => AppSetting::pageSlugs(),
            ],
        ]);
    }

    /**
     * Met à jour le nom de l'app et le titre du navigateur.
     */
    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'required|string|max:60',
            'browser_title' => 'required|string|max:60',
        ]);

        AppSetting::setMany($data);

        return back()->with('success', 'Paramètres généraux mis à jour.');
    }

    /**
     * Upload / remplacement du logo.
     */
    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        // Supprime l'ancien logo s'il existe
        $old = AppSetting::get('logo_path');
        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        $path = $request->file('logo')->store('logos', 'public');
        AppSetting::set('logo_path', $path);

        return back()->with('success', 'Logo mis à jour.');
    }

    public function removeLogo()
    {
        $old = AppSetting::get('logo_path');
        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }
        AppSetting::set('logo_path', null);

        return back()->with('success', 'Logo supprimé.');
    }

    /**
     * Met à jour les slugs d'URL personnalisés pour chaque page.
     * ex: dashboard -> "tableau-de-bord" donnera /tableau-de-bord
     */
    public function updateSlugs(Request $request)
    {
        $data = $request->validate([
            'slugs' => 'required|array',
            'slugs.dashboard' => 'required|string|max:60|regex:/^[a-z0-9\-]+$/',
            'slugs.stock' => 'required|string|max:60|regex:/^[a-z0-9\-]+$/',
            'slugs.weight' => 'required|string|max:60|regex:/^[a-z0-9\-]+$/',
            'slugs.settings' => 'required|string|max:60|regex:/^[a-z0-9\-]+$/',
        ], [
            'slugs.*.regex' => 'Uniquement lettres minuscules, chiffres et tirets.',
        ]);

        $slugs = $data['slugs'];

        // Les valeurs doivent être uniques entre elles
        if (count($slugs) !== count(array_unique($slugs))) {
            return back()->withErrors(['slugs' => 'Les URLs de pages doivent être uniques.']);
        }

        // Conserve les anciens slugs pour créer les redirections 301
        $previous = AppSetting::pageSlugs();
        $redirects = AppSetting::get('slug_redirects');
        $redirects = $redirects ? json_decode($redirects, true) : [];

        foreach ($previous as $page => $oldSlug) {
            $newSlug = $slugs[$page] ?? $oldSlug;
            if ($oldSlug !== $newSlug) {
                $redirects[$oldSlug] = $page; // ancienne url -> nom de page (pour retrouver la nouvelle url dynamiquement)
            }
        }

        AppSetting::set('page_slugs', json_encode($slugs));
        AppSetting::set('slug_redirects', json_encode($redirects));

        return back()->with('success', 'URLs mises à jour. Pense à vider le cache de routes (php artisan route:clear) si besoin.');
    }
}
