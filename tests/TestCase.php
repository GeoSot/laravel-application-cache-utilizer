<?php

namespace GeoSot\AppCache\Tests;

use GeoSot\AppCache\Facades\AppCache;
use GeoSot\AppCache\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;


class TestCase extends OrchestraTestCase
{
    /**
     * @inheritdoc
     */
    protected function getEnvironmentSetUp($app)
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
}
