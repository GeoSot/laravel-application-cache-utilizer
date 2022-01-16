<?php

namespace GeoSot\AppCache\Tests;

use Exception;

class AppCacheTest extends TestCase
{


    /**
     * @test
     */
    public function our_test_helper__makeFilePath__returns_correct_results()
    {
        $filePath = static::makeFilePath('fooKey');
        self::assertSame(static::TEST_DIR.DIRECTORY_SEPARATOR.'fooKey.php', $filePath);
    }

    /**
     * @test
     *
     */
    public function ensures_cache_directory_existence()
    {
        $this->getAppCacheInstance([]);
        self::assertDirectoryExists(static::TEST_DIR);
    }

    /**
     * @test
     */
    public function creates_a_file_including_the_given_key_and_stores_the_given_data()
    {
        $instance = $this->getAppCacheInstance([]);
        $data = $this->getDummyData();
        $result = $instance->add('fooKey', $data);
        self::assertTrue($result);
        $filePath = static::makeFilePath('fooKey');
        self::assertFileExists($filePath);
        self::assertEqualsCanonicalizing($data, require $filePath);
    }


    /**
     * @test
     */
    public function does_not_recreates_a_file_if_exist()
    {
        $instance = $this->getAppCacheInstance([]);
        $data = $this->getDummyData();
        $filePath = static::makeFilePath('fooKey');
        $result = $instance->add('fooKey', $data);
        self::assertTrue($result);

        $result = $instance->add('fooKey', ['anotherData' => 'hmm']);
        self::assertFalse($result);


        self::assertEqualsCanonicalizing($data, require $filePath);
    }

    /**
     * @test
     */
    public function recreates_a_file_if_exist_and_we_use___force__flag()
    {
        $instance = $this->getAppCacheInstance([]);
        $data = $this->getDummyData();
        $filePath = static::makeFilePath('fooKey');
        $result = $instance->add('fooKey', $data);
        self::assertTrue($result);

        $result = $instance->add('fooKey', ['anotherData' => 'hmm'], true);
        self::assertTrue($result);


        self::assertEqualsCanonicalizing(['anotherData' => 'hmm'], require $filePath);
    }

    /**
     * @test
     */
    public function can_handle_adding_stringable_data()
    {
        $instance = $this->getAppCacheInstance([]);
        $data = [
            'test' => 'bar',
            'test2' => new \stdClass(),
            'test3' =>  json_decode('{ "foo": "bar", "number": 42 }')

        ];
        $result = $instance->add('fooKey', $data);
        self::assertTrue($result);
        self::assertEqualsCanonicalizing($data, $instance->get('fooKey'));
    }

    /**
     * @test
     */
    public function returns_false_if_it_fails_to_create_a_valid_file()
    {
        $this->app['config']->set('app-cache-utilizer.throw_exception_if_add_fails', false);
        $instance = $this->getAppCacheInstance([]);
        $data = [
            'test' => fn() => 111,
        ];
        $filePath = static::makeFilePath('fooKey');
        $result = $instance->add('fooKey', $data);
        self::assertFalse($result);
        self::assertFileDoesNotExist($filePath);
    }

    /**
     * @test
     */
    public function throws_exception_if_it_fails_to_create_a_valid_file()
    {
        $this->app['config']->set('app-cache-utilizer.throw_exception_if_add_fails', true);
        $instance = $this->getAppCacheInstance([]);
        $data = [
            'test' => fn() => 111,
        ];
        self::expectException(\LogicException::class);
        $instance->add('fooKey', $data);
    }


    /**
     * @test
     */
    public function returns_a_bool_according_to_the_existence_of_the_file()
    {
        $instance = $this->getAppCacheInstance([]);
        $data = $this->getDummyData();
        $filePath = static::makeFilePath('fooKey');

        self::assertFalse($instance->has('fooKey'));
        self::assertFileDoesNotExist($filePath);

        $result = $instance->add('fooKey', $data);

        $instance->add('fooKey', $data);
        self::assertTrue($instance->has('fooKey'));
        self::assertFileExists($filePath);
    }


    /**
     * @test
     */
    public function returns_the_saved_data()
    {
        $instance = $this->getAppCacheInstance([]);
        $data = $this->getDummyData();

        $instance->add('fooKey', $data);

        $result = $instance->get('fooKey');
        self::assertEqualsCanonicalizing($data, $result);
    }

    /**
     * @test
     */
    public function returns_the_default_in_case_of_not_saved_data()
    {
        $instance = $this->getAppCacheInstance([]);
        $data = $this->getDummyData();

        $instance->has('fooKey');

        $result = $instance->get('fooKey', $data);
        self::assertEqualsCanonicalizing($data, $result);
    }


    /**
     * @test
     */
    public function deletes_the_file_according_to_given_key()
    {
        $instance = $this->getAppCacheInstance([]);
        $instance->add('fooKey', $this->getDummyData());
        self::assertTrue($instance->has('fooKey'));

        self::assertTrue($instance->forget('fooKey'));

        self::assertFalse($instance->has('fooKey'));
        $filePath = static::makeFilePath('fooKey');
        self::assertFileDoesNotExist($filePath);
    }

    /**
     * @test
     */
    public function deletes_all_the_saved_files_inside_the_directory()
    {
        $instance = $this->getAppCacheInstance([]);
        $instance->add('fooKey', $this->getDummyData());
        $instance->add('fooKey1', $this->getDummyData());
        $instance->add('fooKey2', $this->getDummyData());

        self::assertFileExists(static::makeFilePath('fooKey'));
        self::assertFileExists(static::makeFilePath('fooKey1'));
        self::assertFileExists(static::makeFilePath('fooKey2'));

        self::assertTrue($instance->clear());


        self::assertFileDoesNotExist(static::makeFilePath('fooKey'));
        self::assertFileDoesNotExist(static::makeFilePath('fooKey1'));
        self::assertFileDoesNotExist(static::makeFilePath('fooKey2'));

        self::assertDirectoryExists(static::TEST_DIR);
    }

    /**
     * @test
     */
    public function can_cache_data_if_not_exists_and_return_them()
    {
        $instance = $this->getAppCacheInstance([]);
        self::assertEmpty($instance->get('fooKey'));

        $data=$this->getDummyData();

        $result=$instance->remember('fooKey', fn()=>$data);


        self::assertEqualsCanonicalizing($data, $instance->get('fooKey'));
        self::assertEqualsCanonicalizing($data, $result);


        $result=$instance->remember('fooKey', fn()=>['otherData'=>'test']);
        self::assertEqualsCanonicalizing($data, $result);
    }


    /**
     * @return array<string, mixed>
     */
    protected function getDummyData(): array
    {
        return [
            'foo' => 'bar',
            'test' => 'foo',
        ];
    }


}
