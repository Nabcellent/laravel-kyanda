<?php

namespace Nabcellent\Kyanda\Tests;

use Nabcellent\Kyanda\KyandaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Class TestCase
 * @package Nabcellent\Tests
 */
abstract class TestCase extends Orchestra
{

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
