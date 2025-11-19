<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
  use RefreshDatabase;

  public function test_database_is_seeded_correctly()
  {

    // Test if the necessary data is seeded
    $this->assertDatabaseHas('users', [
      'email' => 'demo@example.com',
    ]);
  }
}
