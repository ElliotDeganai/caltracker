<?php

use App\Http\Controllers\CalorieTrackerController;
use App\Http\Controllers\FrozenMealController;
use App\Http\Controllers\CalorieTargetController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserSwitchController;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes principales de l'app, avec slugs personnalisables via Settings
| ET permissions via spatie/laravel-permission.
|--------------------------------------------------------------------------
*/

if (! function_exists('slug')) {
    function slug(string $page): string
    {
        try {
            return AppSetting::slug($page);
        } catch (\Throwable $e) {
            return $page;
        }
    }
}

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/admin/switch-user', [UserSwitchController::class, 'switch'])
    ->middleware('permission:manage-users')
    ->name('admin.switch-user');
    Route::post('/admin/clear-user', [UserSwitchController::class, 'clear'])
        ->middleware('permission:manage-users')
        ->name('admin.clear-user');

    Route::get('/' . slug('dashboard'), [CalorieTrackerController::class, 'index'])
        ->middleware('permission:view-dashboard')
        ->name('dashboard');

    Route::post('/calories', [CalorieTrackerController::class, 'storeCalories'])
        ->middleware('permission:log-calories')
        ->name('calories.store');

    Route::post('/poids', [CalorieTrackerController::class, 'storeWeight'])
        ->middleware('permission:log-weight')
        ->name('weight.store');

    // Stock congélateur - page dédiée
    Route::get('/' . slug('stock'), [FrozenMealController::class, 'index'])
        ->middleware('permission:view-stock')
        ->name('stock.index');
    Route::post('/stock', [FrozenMealController::class, 'store'])
        ->middleware('permission:manage-stock')
        ->name('stock.store');
    Route::put('/stock/{frozenMeal}', [FrozenMealController::class, 'update'])
        ->middleware('permission:manage-stock')
        ->name('stock.update');
    Route::delete('/stock/{frozenMeal}', [FrozenMealController::class, 'destroy'])
        ->middleware('permission:manage-stock')
        ->name('stock.destroy');
    Route::post('/stock/{frozenMeal}/consume', [FrozenMealController::class, 'consume'])
        ->middleware('permission:consume-stock')
        ->name('stock.consume');

    // Objectifs caloriques hebdomadaires
    Route::get('/objectifs', [CalorieTargetController::class, 'index'])
        ->middleware('permission:view-targets')
        ->name('targets.index');
    Route::post('/objectifs', [CalorieTargetController::class, 'store'])
        ->middleware('permission:manage-targets')
        ->name('targets.store');
    Route::delete('/objectifs/{calorieTarget}', [CalorieTargetController::class, 'destroy'])
        ->middleware('permission:manage-targets')
        ->name('targets.destroy');

    // Configuration de l'application
    Route::get('/' . slug('settings'), [SettingsController::class, 'index'])
        ->middleware('permission:view-settings')
        ->name('settings.index');
    Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])
        ->middleware('permission:manage-settings')
        ->name('settings.general');
    Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])
        ->middleware('permission:manage-settings')
        ->name('settings.logo');
    Route::delete('/settings/logo', [SettingsController::class, 'removeLogo'])
        ->middleware('permission:manage-settings')
        ->name('settings.logo.remove');
    Route::post('/settings/urls', [SettingsController::class, 'updateSlugs'])
        ->middleware('permission:manage-settings')
        ->name('settings.slugs');
});

Route::get('/calories/{date}', [CalorieTrackerController::class, 'getCalories'])
    ->middleware('permission:log-calories')
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('calories.get');

Route::get('/poids/{date}', [CalorieTrackerController::class, 'getWeight'])
    ->middleware('permission:log-weight')
    ->where('date', '\d{4}-\d{2}-\d{2}')
    ->name('weight.get');

Route::get('/calories/week/{offset}', [CalorieTrackerController::class, 'getCaloriesWeek'])
    ->middleware('permission:view-dashboard')
    ->where('offset', '\d+')
    ->name('calories.week');

Route::get('/poids/week/{offset}', [CalorieTrackerController::class, 'getWeightWeek'])
    ->middleware('permission:view-dashboard')
    ->where('offset', '\d+')
    ->name('weight.week');
