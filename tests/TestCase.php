<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $app = parent::createApplication();

        if ($app->environment() !== 'testing'
            || config('database.default') !== 'pgsql'
            || config('database.connections.pgsql.database') !== 'sibol_testing') {
            throw new RuntimeException('Tests may only use the sibol_testing PostgreSQL database. Clear any cached configuration.');
        }

        return $app;
    }
}
