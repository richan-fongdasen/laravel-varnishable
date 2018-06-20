<?php

namespace RichanFongdasen\Varnishable\Tests;

use Orchestra\Testbench\TestCase as BaseTest;

abstract class TestCase extends BaseTest
{
    /**
     * Application object
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Define environment setup
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->app = $app;

        $app['config']->set('varnishable.application_hosts', 'localhost:8000');
    }

    /**
     * Define package aliases
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        $this->app = $app;

        return [
            'Varnishable' => \RichanFongdasen\Varnishable\Facade::class,
        ];
    }

    /**
     * Define package service provider
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        $this->app = $app;
        
        return [
            \RichanFongdasen\Varnishable\ServiceProvider::class,
        ];
    }

    /**
     * Invoke protected / private method of the given object
     *
     * @param  Object      $object
     * @param  String      $methodName
     * @param  Array|array $parameters
     * @return mixed
     */
    protected function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Get any protected / private property value
     *
     * @param  mixed $object
     * @param  string $propertyName
     * @return mixed
     */
    public function getPropertyValue($object, $propertyName)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
 
        return $property->getValue($object);
    }

    /**
     * Prepare to get an exception in a test
     *
     * @param  mixed $exception
     * @return void
     */
    protected function prepareException($exception)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($exception);
        } else {
            $this->setExpectedException($exception);
        }
    }

    /**
     * Setup the test environment
     */
    public function setUp()
    {
        parent::setUp();
    }
}
