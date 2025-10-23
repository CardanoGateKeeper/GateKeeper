<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Policy;

class EventControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function test_create_event()
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $eventData = [
      'name' => 'Gatekeeper Test Event',
      'event_date' => '2025-06-01',
      'start_date_time' => '2025-06-01 09:00:00',
      'end_date_time' => '2025-06-01 17:00:00',
      'location' => 'Test Venue TBA',
    ];

    $response = $this->post(route('manage-event.store'), $eventData);

    $response->assertStatus(302);
    $this->assertDatabaseHas('events', ['name' => 'New Event']);
  }

  /** @test */
  public function test_view_event()
  {
    $event = Event::factory()->create();
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('manage-event.show', $event->uuid));

    $response->assertStatus(200);
    $response->assertViewIs('ManageEvent/Show');
  }
}
