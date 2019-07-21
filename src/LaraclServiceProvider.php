<?php

namespace Orendo\LaravelPackage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\PresetCommand;
use Orendo\LaravelPackage\Acl\AclSetup;
use Orendo\LaravelPackage\Tailwind\Tailwind;

class LaraclServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AclSetup::class,
            ]);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
