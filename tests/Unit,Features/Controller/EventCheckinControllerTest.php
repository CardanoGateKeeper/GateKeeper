<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Policy;

class EventCheckinControllerTest extends TestCase
{
  /** @test */
  public function index_displays_events_for_current_team()
  {
    $response = $this->get(route('event.check-in.index', ['event' => 1]));

    $response->assertStatus(200);
    $response->assertViewHas('events');
  }

  /** @test */
  public function show_displays_single_event()
  {
    $response = $this->get(route('event.check-in.show', ['event' => 1]));

    $response->assertStatus(200);
    $response->assertViewHas('event');
  }
}
