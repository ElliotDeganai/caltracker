<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FrozenMeal extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'calories_per_portion', 'portions'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function totalCalories(): int
    {
        return $this->calories_per_portion * $this->portions;
    }
}
