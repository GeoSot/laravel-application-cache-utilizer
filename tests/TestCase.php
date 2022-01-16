<?php

namespace GeoSot\AppCache\Tests;


use GeoSot\AppCache\AppCache;
use GeoSot\AppCache\ServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as OrchestraTestCase;


abstract class TestCase extends OrchestraTestCase
{
    protected const TEST_DIR = __DIR__.DIRECTORY_SEPARATOR.'test-our-package';


    protected function tearDown(): void
    {
        File::deleteDirectory(static::TEST_DIR);

        parent::tearDown();
    }


    /**
     * @inheritdoc
     */
    protected function defineEnvironment($app)
    {
    }


    /**
     * @inheritdoc
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getAppCacheInstance(array $config = []): AppCache
    {
        $instance = new AppCache(
            static::TEST_DIR,
            new Repository($config ?: config('app-cache-utilizer', [])),
            $this->app->make(Filesystem::class)
        );
        $this->app->bind(AppCache::class, fn() => $instance);
        return $instance;
    }

    protected function makeFilePath(string $key): string
    {
        return static::TEST_DIR.DIRECTORY_SEPARATOR.$key.'.php';
    }
}
