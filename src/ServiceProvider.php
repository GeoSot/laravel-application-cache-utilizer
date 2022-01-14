<?php

namespace GeoSot\AppCache;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Vendor name.
     *
     * @var string
     */
    protected $vendor = 'geo-sv';

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
            $cacheDirectory=app()->bootstrapPath($this->package);
            return $this->app->make(AppCache::class,[$cacheDirectory,config($this->package)]);

//            return new AppCache(config($this->package), $this->app->bootstrapPath());

        });

    }

}
