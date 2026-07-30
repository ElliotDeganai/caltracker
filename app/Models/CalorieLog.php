<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalorieLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'calories', 'note'];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Moyenne lissée sur 7 jours glissants se terminant à $date (incluse).
     * Ne prend en compte que les jours où une valeur a été renseignée
     * (un jour non renseigné n'est PAS compté comme 0, il est ignoré).
     * Retourne null si aucun jour n'a de donnée sur la fenêtre.
     */
    public static function rollingAverage(int $userId, \DateTimeInterface $date, int $window = 7): ?float
    {
        $start = (clone $date)->modify('-' . ($window - 1) . ' days');

        $values = static::where('user_id', $userId)
            ->whereBetween('date', [$start->format('Y-m-d'), $date->format('Y-m-d')])
            ->pluck('calories');

        if ($values->isEmpty()) {
            return null;
        }

        return round($values->avg(), 0);
    }

    /**
     * Nombre de jours renseignés sur la fenêtre.
     */
    public static function rollingCount(int $userId, \DateTimeInterface $date, int $window = 7): int
    {
        $start = (clone $date)->modify('-' . ($window - 1) . ' days');

        return static::where('user_id', $userId)
            ->whereBetween('date', [$start->format('Y-m-d'), $date->format('Y-m-d')])
            ->count();
    }

    /**
     * Suggestion calorique du jour pour respecter la cible sur la fenêtre glissante.
     * Les jours passés NON renseignés ne sont pas traités comme "0 kcal consommé"
     * (ce qui gonflerait artificiellement le budget restant) : ils sont ajoutés
     * au nombre de jours sur lesquels répartir le budget restant, au même titre
     * qu'aujourd'hui.
     */
    public static function suggestedTargetForToday(int $userId, int $weeklyTarget, \DateTimeInterface $today, int $window = 7): int
    {
        $start = (clone $today)->modify('-' . ($window - 1) . ' days');
        $end = (clone $today)->modify('-1 day');

        $pastLogs = static::where('user_id', $userId)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->pluck('calories');

        $consumed = $pastLogs->sum();
        $trackedPastDays = $pastLogs->count();
        $untrackedPastDays = ($window - 1) - $trackedPastDays;

        $budget = $weeklyTarget * $window;
        $remaining = $budget - $consumed;
        $remainingDays = 1 + $untrackedPastDays; // aujourd'hui + jours passés non renseignés

        return max(0, (int) round($remaining / max(1, $remainingDays)));
    }
}
