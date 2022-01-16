<?php

namespace GeoSot\AppCache\Tests;

use GeoSot\AppCache\ClearCommand;
use Illuminate\Support\Facades\Artisan;


class ClearCommandTest extends TestCase
{

    /**
     * @test
     */
    public function command_is_registered()
    {
        self::assertArrayHasKey('app-cache-utilizer:clear', Artisan::all());
    }


    /**
     * @test
     */
    public function clears_all_cached_files()
    {
        $instance = $this->getAppCacheInstance([]);

        $instance->add('fooKey', ['foo' => 'bar']);
        $instance->add('fooKey1', ['foo' => 'bar']);
        $instance->add('fooKey1', ['foo' => 'bar']);

        $command = $this->artisan(ClearCommand::class);
        $command->execute();


        self::assertFileDoesNotExist(static::makeFilePath('fooKey'));
        self::assertFileDoesNotExist(static::makeFilePath('fooKey1'));
        self::assertFileDoesNotExist(static::makeFilePath('fooKey2'));

        self::assertDirectoryExists(static::TEST_DIR);

        $command
            ->assertSuccessful()
            ->expectsOutput('Configuration cache cleared');
    }

    /**
     * @test
     */
    public function clears_scoped_cached_file()
    {
        $instance = $this->getAppCacheInstance([]);

        $instance->add('fooKey', ['foo' => 'bar']);
        $instance->add('fooKey1', ['foo' => 'bar']);

        $command = $this->artisan('app-cache-utilizer:clear', ['key' => 'fooKey']);
        $command->execute();

        self::assertFileDoesNotExist(static::makeFilePath('fooKey'));
        self::assertFileExists(static::makeFilePath('fooKey1'));

        self::assertDirectoryExists(static::TEST_DIR);
        $command
            ->assertSuccessful()
            ->expectsOutput("Configuration cache key:'fooKey' cleared!");
    }
}
