<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CalorieTarget extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'week_start_date', 'target_calories'];

    protected $casts = [
        'week_start_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retourne la cible active pour une date donnée
     * (la cible dont week_start_date est la plus récente <= date).
     */
    public static function activeForDate(int $userId, \DateTimeInterface $date): ?self
    {
        return static::where('user_id', $userId)
            ->whereDate('week_start_date', '<=', Carbon::parse($date)->format('Y-m-d'))
            ->orderByDesc('week_start_date')
            ->first();
    }

    public static function currentWeekStart(\DateTimeInterface $date = null): string
    {
        return Carbon::parse($date ?? now())->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
    }
}
