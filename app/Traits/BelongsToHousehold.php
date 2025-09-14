<?php

namespace App\Traits;

use App\Models\Household;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToHousehold
{
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function scopeForCurrentHousehold(Builder $query): Builder
    {
        $user = Auth::user();
        
        if ($user && $user->household_id) {
            return $query->where('household_id', $user->household_id);
        }

        return $query->whereNull('household_id');
    }

    protected static function bootBelongsToHousehold(): void
    {
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->household_id) {
                $model->household_id = Auth::user()->household_id;
            }
        });
    }
}
