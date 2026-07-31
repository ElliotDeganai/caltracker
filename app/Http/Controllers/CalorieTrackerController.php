<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTargetUser;
use App\Models\CalorieLog;
use App\Models\CalorieTarget;
use App\Models\WeightLog;
use App\Models\FrozenMeal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalorieTrackerController extends Controller
{
    use ResolvesTargetUser;

    public function index(Request $request)
    {
        $userId = $this->targetUserId($request);
        $today = Carbon::today();

        $last7 = collect(range(6, 0))->map(function ($daysAgo) use ($userId, $today) {
            $date = (clone $today)->subDays($daysAgo);
            $log = CalorieLog::where('user_id', $userId)->whereDate('date', $date)->first();

            return [
                'date' => $date->format('Y-m-d'),
                'label' => $date->locale('fr')->translatedFormat('D'),
                'calories' => $log?->calories,
            ];
        });

        $target = CalorieTarget::activeForDate($userId, $today);
        $weeklyTarget = $target?->target_calories ?? 2100;

        $rollingAvg = CalorieLog::rollingAverage($userId, $today, 7);
        $rollingCount = CalorieLog::rollingCount($userId, $today, 7);
        $suggestion = CalorieLog::suggestedTargetForToday($userId, $weeklyTarget, $today, 7);

        $todayLog = CalorieLog::where('user_id', $userId)->whereDate('date', $today)->first();

        // Poids - 7 derniers jours
        $last7Weights = collect(range(6, 0))->map(function ($daysAgo) use ($userId, $today) {
            $date = (clone $today)->subDays($daysAgo);
            $log = WeightLog::where('user_id', $userId)->whereDate('date', $date)->first();
            return [
                'date' => $date->format('Y-m-d'),
                'label' => $date->locale('fr')->translatedFormat('D'),
                'weight' => $log?->weight_kg,
                'smoothed' => WeightLog::rollingAverage($userId, $date, 7),
            ];
        });

        $smoothedToday = WeightLog::rollingAverage($userId, $today, 7);
        $smoothedLastWeek = WeightLog::rollingAverage($userId, (clone $today)->subDays(7), 7);
        $weightDelta = ($smoothedToday !== null && $smoothedLastWeek !== null)
            ? round($smoothedToday - $smoothedLastWeek, 2)
            : null;

        $todayWeight = WeightLog::where('user_id', $userId)->whereDate('date', $today)->first();

        // Moyenne de poids des 4 dernières semaines (lundi-dimanche)
        $last4Weeks = collect(range(3, 0))->map(function ($weeksAgo) use ($userId, $today) {
            $weekStart = (clone $today)->startOfWeek(Carbon::MONDAY)->subWeeks($weeksAgo);
            $weekEnd = (clone $weekStart)->endOfWeek(Carbon::SUNDAY);

            $values = WeightLog::where('user_id', $userId)
                ->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
                ->pluck('weight_kg');

            return [
                'week_start' => $weekStart->format('d/m'),
                'week_end' => $weekEnd->format('d/m'),
                'average' => $values->isEmpty() ? null : round($values->avg(), 2),
                'days_logged' => $values->count(),
            ];
        });

        $stockTotal = FrozenMeal::where('user_id', $userId)->get()
            ->reduce(fn ($carry, $meal) => $carry + $meal->totalCalories(), 0);
        $stockPortions = FrozenMeal::where('user_id', $userId)->sum('portions');

        return Inertia::render('Dashboard', [
            'last7' => $last7,
            'rollingAvg' => $rollingAvg,
            'rollingCount' => $rollingCount,
            'weeklyTarget' => $weeklyTarget,
            'suggestion' => $suggestion,
            'todayCalories' => $todayLog?->calories,
            'last7Weights' => $last7Weights,
            'smoothedToday' => $smoothedToday,
            'weightDelta' => $weightDelta,
            'todayWeight' => $todayWeight?->weight_kg,
            'last4Weeks' => $last4Weeks,
            'stockTotal' => $stockTotal,
            'stockPortions' => $stockPortions,
        ]);
    }

    public function storeCalories(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'calories' => 'required|integer|min:0|max:10000',
            'note' => 'nullable|string|max:255',
        ]);

        CalorieLog::updateOrCreate(
            ['user_id' => $this->targetUserId($request), 'date' => $data['date']],
            ['calories' => $data['calories'], 'note' => $data['note'] ?? null]
        );

        return back()->with('success', 'Calories mises à jour !');
    }

    public function storeWeight(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'weight_kg' => 'required|numeric|min:20|max:400',
        ]);

        WeightLog::updateOrCreate(
            ['user_id' => $this->targetUserId($request), 'date' => $data['date']],
            ['weight_kg' => $data['weight_kg']]
        );

        return back()->with('success', 'Poids mis à jour !');
    }

    public function getCalories(Request $request, string $date)
    {
        $userId = $this->targetUserId($request);
        $log = CalorieLog::where('user_id', $userId)->whereDate('date', $date)->first();

        return response()->json([
            'calories' => $log?->calories,
            'note' => $log?->note,
        ]);
    }

    public function getWeight(Request $request, string $date)
    {
        $userId = $this->targetUserId($request);
        $log = WeightLog::where('user_id', $userId)->whereDate('date', $date)->first();

        return response()->json([
            'weight_kg' => $log?->weight_kg,
        ]);
    }
}
