<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    const CACHE_KEY = 'app_settings_all';

    /**
     * Retourne toutes les settings sous forme de tableau clé => valeur, mis en cache.
     */
    public static function allCached(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return static::pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            // Table pas encore migrée (ex: pendant `artisan migrate` initial)
            return [];
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::allCached()[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Slugs de pages personnalisés, ex: ['dashboard' => 'tableau-de-bord', 'stock' => 'stock']
     */
    public static function pageSlugs(): array
    {
        $raw = self::get('page_slugs');
        $defaults = [
            'dashboard' => 'dashboard',
            'stock' => 'stock',
            'weight' => 'poids',
            'settings' => 'parametres',
        ];

        if (!$raw) {
            return $defaults;
        }

        $decoded = json_decode($raw, true) ?: [];

        return array_merge($defaults, $decoded);
    }

    public static function slug(string $page): string
    {
        return self::pageSlugs()[$page] ?? $page;
    }
}
