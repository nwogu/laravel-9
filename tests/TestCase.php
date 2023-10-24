<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setup(): void
    {
        parent::setUp();

        $this->app->bind(
            \App\Utilities\Contracts\RedisHelperInterface::class,
            \Tests\RedisHelper::class
        );

        $this->app->bind(
            \App\Utilities\Contracts\ElasticsearchHelperInterface::class,
            \Tests\ElasticsearchHelper::class
        );
    }
}
