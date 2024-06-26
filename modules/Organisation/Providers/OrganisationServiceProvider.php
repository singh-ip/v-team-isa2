<?php

namespace Modules\Organisation\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\ModuleService;
use Modules\Organisation\Models\Organisation;
use Modules\Team\Models\Team;

class OrganisationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'team');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        if (ModuleService::isEnabled('Team')) {
            Team::resolveRelationUsing('organisation', function ($teamModel) {
                return $teamModel->belongsTo(Organisation::class);
            });
        }
    }
}