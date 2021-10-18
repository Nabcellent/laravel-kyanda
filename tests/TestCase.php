<?php

namespace Nabcellent\Tests;

use Nabcellent\src\KyandaServiceProvider;

/**
 * Class TestCase
 * @package Nabcellent\Tests
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app): array
    {
        return [
            KyandaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
