<?php

namespace Orendo\LaravelPackage\Acl;

use Illuminate\Support\Facades\File;

class Assets
{
    public static function installAssets()
    {
        static::publishConfig();
        static::publishMail();
        static::publishMiddleware();
        static::publishCommands();
        static::publishLogViewer();
        static::publishKernel();
        static::publishController();
        static::publishModels();
        static::publishMigrations();
        static::publishRoutes();
        static::publishProfileStorage();
    }

    public static function publishConfig()
    {
        File::copy(__DIR__.'/Config/acl.php', config_path('acl.php'));
        File::copy(__DIR__.'/Config/logging.php', config_path('logging.php'));
    }

    public static function publishMail()
    {
        if (!File::isDirectory(base_path('app/Mail'))) {
            File::makeDirectory(base_path('app/Mail'));
        }

        File::copyDirectory(__DIR__.'/Mail', base_path('app/Mail'));
    }

    public static function publishMiddleware()
    {
        File::copyDirectory(__DIR__.'/Middleware', base_path('app/Http/Middleware'));
    }

    public static function publishCommands()
    {
        File::copyDirectory(__DIR__.'/Commands', base_path('app/Console/Commands'));
    }

    public static function publishLogViewer()
    {
        File::copyDirectory(__DIR__.'/Logs', base_path('app/Logs'));
    }

    public static function publishKernel()
    {
        if (File::exists(base_path('app/Http/Kernel.php'))) {
            File::delete(base_path('app/Http/Kernel.php'));
        }

        File::copy(__DIR__.'/Kernel/Kernel.php', base_path('app/Http/Kernel.php'));
    }

    public static function publishController()
    {
        if (File::cleanDirectory(base_path('app/Http/Controllers'))) {
            File::copyDirectory(__DIR__.'/Controller', base_path('app/Http/Controllers'));
        }
    }

    public static function publishModels()
    {
        if (File::exists(base_path('app/User.php'))) {
            File::delete(base_path('app/User.php'));
        }

        File::copyDirectory(__DIR__.'/Models', base_path('app'));
    }

    public static function publishMigrations()
    {
        File::cleanDirectory(database_path('migrations'));

        File::copyDirectory(__DIR__.'/Migrations', database_path('migrations'));
    }

    public static function publishRoutes()
    {
        if (File::exists(base_path('routes/web.php'))) {
            File::delete(base_path('routes/web.php'));
        }

        File::copy(__DIR__.'/Routes/web.php', base_path('routes/web.php'));
    }

    public static function publishProfileStorage()
    {
        if (File::isDirectory(storage_path('public/profile'))) {
            File::cleanDirectory(storage_path('public/profile'));
        }

        File::copyDirectory(__DIR__.'/Storage/profile', storage_path('app/public/profile'));
    }

    public static function publishAuthServiceProvider()
    {
        if (File::exists(base_path('app/Providers/AuthServiceProvider.php'))) {
            File::delete(base_path('app/Providers/AuthServiceProvider.php'));
        }
        File::copy(__DIR__.'/Provider/AuthServiceProvider.php', base_path('app/Providers/AuthServiceProvider.php'));
    }
}
