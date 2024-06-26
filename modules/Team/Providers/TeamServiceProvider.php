<?php

namespace Modules\Team\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Modules\Team\Models\Team;

class TeamServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'team');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        User::resolveRelationUsing('teams', function ($userModel) {
            return $userModel->belongsToMany(Team::class);
        });
    }
}