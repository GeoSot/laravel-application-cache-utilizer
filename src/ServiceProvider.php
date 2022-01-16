<?php

namespace GeoSot\AppCache;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'app-cache-utilizer';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__."/../config/{$this->package}.php" => config_path($this->package.'.php'),
            ], 'config');

            $this->commands(
                ClearCommand::class
            );
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__."/../config/{$this->package}.php",
            $this->package
        );

        $this->app->singleton(AppCache::class, function () {
            $cacheDirectory = app()->bootstrapPath($this->package);

            return new AppCache(
                $cacheDirectory,
                new Repository(config($this->package)),
                $this->app->make(Filesystem::class)
            );
        });
    }
}
