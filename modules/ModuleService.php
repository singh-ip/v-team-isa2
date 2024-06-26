<?php

namespace Modules;

class ModuleService
{
    public static function enabled(): array
    {
        return array_values(array_filter(static::list(), static::isEnabled(...)));
    }

    public static function isEnabled(string $moduleName): bool
    {
        return app()->getLoadedProviders()["Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider"] ?? false;
    }

    public static function list(): array
    {
        return array_map('basename', glob(__DIR__ . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR));
    }
}