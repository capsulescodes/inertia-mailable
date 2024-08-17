<?php

namespace CapsulesCodes\InertiaMailable\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use CapsulesCodes\InertiaMailable\Providers\InertiaMailableServiceProvider;


abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders( $app ) : array
    {
        return [ InertiaMailableServiceProvider::class ];
    }
}
