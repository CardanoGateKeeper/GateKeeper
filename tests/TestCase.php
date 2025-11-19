<?php

namespace Tests;

use Database\Seeders\DemoEventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\WithoutMiddleware;

abstract class TestCase extends BaseTestCase
{
  use CreatesApplication;
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();

    $this->seed(DemoEventSeeder::class);
  }

  public function createApplication()
  {
    $app = require __DIR__ . '/../bootstrap/app.php';

    $app->loadEnvironmentFrom('.env.testing');

    $app->make(Kernel::class)->bootstrap();

    return $app;
  }
}
