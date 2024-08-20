<?php

namespace CapsulesCodes\InertiaMailable\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Inertia\ServiceProvider as InertiaServiceProvider;
use CapsulesCodes\InertiaMailable\Providers\ServiceProvider;


abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders( $app ) : array
    {
        return [ InertiaServiceProvider::class, ServiceProvider::class ];
    }
}
