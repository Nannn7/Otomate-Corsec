<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        /** @var Application $app */
        $app = parent::createApplication();

        $this->assertSafeTestingDatabaseConfiguration($app);

        return $app;
    }

    private function assertSafeTestingDatabaseConfiguration(Application $app): void
    {
        if (!$app->environment('testing')) {
            return;
        }

        $defaultConnection = (string) $app['config']->get('database.default');

        if ($defaultConnection === 'sqlite') {
            $database = (string) $app['config']->get('database.connections.sqlite.database');
            $normalizedPath = str_replace('\\', '/', $database);

            if ($database === ':memory:' || Str::endsWith($normalizedPath, '/testing.sqlite')) {
                return;
            }

            throw new RuntimeException(
                "Unsafe sqlite testing database [{$database}]. Use :memory: or a dedicated testing.sqlite file."
            );
        }

        $database = (string) $app['config']->get("database.connections.{$defaultConnection}.database", '');
        $requiredSuffix = (string) env('TEST_DATABASE_SUFFIX', '_test');

        if ($database === '') {
            throw new RuntimeException('Testing database is not configured.');
        }

        if (!Str::endsWith($database, $requiredSuffix)) {
            throw new RuntimeException(
                "Refusing to run tests against database [{$database}]. Configure a dedicated testing database ending with [{$requiredSuffix}]."
            );
        }
    }
}
