<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'date', 'weight_kg'];

    protected $casts = [
        'date' => 'date',
        'weight_kg' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Poids lissé sur 7 jours glissants se terminant à $date (incluse).
     * Moyenne calculée uniquement sur les jours où une pesée existe.
     */
    public static function rollingAverage(int $userId, \DateTimeInterface $date, int $window = 7): ?float
    {
        $start = (clone $date)->modify('-' . ($window - 1) . ' days');

        $logs = static::where('user_id', $userId)
            ->whereBetween('date', [$start->format('Y-m-d'), $date->format('Y-m-d')])
            ->pluck('weight_kg');

        if ($logs->isEmpty()) {
            return null;
        }

        return round($logs->avg(), 2);
    }
}
