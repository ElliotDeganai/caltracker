<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesTargetUser;
use App\Models\CalorieTarget;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalorieTargetController extends Controller
{
    use ResolvesTargetUser;

    public function index(Request $request)
    {
        $userId = $this->targetUserId($request);

        $targets = CalorieTarget::where('user_id', $userId)
            ->orderByDesc('week_start_date')
            ->limit(12)
            ->get();

        return Inertia::render('Targets', [
            'targets' => $targets,
            'currentWeekStart' => CalorieTarget::currentWeekStart(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'week_start_date' => 'required|date',
            'target_calories' => 'required|integer|min:800|max:6000',
        ]);

        CalorieTarget::updateOrCreate(
            ['user_id' => $this->targetUserId($request), 'week_start_date' => $data['week_start_date']],
            ['target_calories' => $data['target_calories']]
        );

        return back();
    }

    public function destroy(Request $request, CalorieTarget $calorieTarget)
    {
        abort_if($calorieTarget->user_id !== $this->targetUserId($request), 403);
        $calorieTarget->delete();

        return back();
    }
}
