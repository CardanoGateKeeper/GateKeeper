<?php

namespace Tests\Unit\Controller;

use App\Http\Controllers\EventController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use App\Models\Policy;

class EventPublicViewTest extends TestCase
{
  use RefreshDatabase;
  /** @test */
  public function controller_class_exists()
  {
    $this->assertTrue(class_exists(EventController::class));
  }

  /** @test */
  public function show_method_exists()
  {
    $this->assertTrue(method_exists(EventController::class, 'show'));
  }
}
