<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTargetUser;
use App\Models\FrozenMeal;
use App\Models\CalorieLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class FrozenMealController extends Controller
{
    use ResolvesTargetUser;

    public function index(Request $request)
    {
        $userId = $this->targetUserId($request);

        $meals = FrozenMeal::where('user_id', $userId)
            ->orderBy('name')
            ->get()
            ->map(fn ($meal) => [
                'id' => $meal->id,
                'name' => $meal->name,
                'calories_per_portion' => $meal->calories_per_portion,
                'portions' => $meal->portions,
                'total_calories' => $meal->totalCalories(),
            ]);

        return Inertia::render('Stock', [
            'meals' => $meals,
            'totalPortions' => $meals->sum('portions'),
            'totalCalories' => $meals->sum('total_calories'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'calories_per_portion' => 'required|integer|min:1|max:5000',
            'portions' => 'required|integer|min:1|max:200',
        ]);

        FrozenMeal::create($data + ['user_id' => $this->targetUserId($request)]);

        return back();
    }

    public function update(Request $request, FrozenMeal $frozenMeal)
    {
        $this->authorizeOwnership($request, $frozenMeal);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'calories_per_portion' => 'required|integer|min:1|max:5000',
            'portions' => 'required|integer|min:0|max:200',
        ]);

        $frozenMeal->update($data);

        return back();
    }

    public function destroy(Request $request, FrozenMeal $frozenMeal)
    {
        $this->authorizeOwnership($request, $frozenMeal);
        $frozenMeal->delete();

        return back();
    }

    /**
     * Décrémente une portion et journalise les calories du jour (repas mangé).
     */
    public function consume(Request $request, FrozenMeal $frozenMeal)
    {
        $this->authorizeOwnership($request, $frozenMeal);

        if ($frozenMeal->portions < 1) {
            return back()->withErrors(['portions' => 'Plus de portions disponibles.']);
        }

        $frozenMeal->decrement('portions');

        $userId = $this->targetUserId($request);
        $today = Carbon::today()->format('Y-m-d');
        $existing = CalorieLog::where('user_id', $userId)->whereDate('date', $today)->first();

        CalorieLog::updateOrCreate(
            ['user_id' => $userId, 'date' => $today],
            ['calories' => ($existing?->calories ?? 0) + $frozenMeal->calories_per_portion]
        );

        return back();
    }

    private function authorizeOwnership(Request $request, FrozenMeal $frozenMeal): void
    {
        abort_if($frozenMeal->user_id !== $this->targetUserId($request), 403);
    }
}
