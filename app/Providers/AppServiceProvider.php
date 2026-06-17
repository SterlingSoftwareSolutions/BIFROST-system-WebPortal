<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'amrap' => \App\Models\Amrap::class,
            'emom' => \App\Models\Emom::class,
            'for-time' => \App\Models\ForTime::class,
            'pyramid' => \App\Models\Pyramid::class,
            'strength' => \App\Models\Strength::class,
            'rounds' => \App\Models\Round::class,
            'intervals' => \App\Models\Interval::class,
            'straight-sets' => \App\Models\StraightSet::class,
            'circuit' => \App\Models\Circuit::class,
        ]);
    }
}
