<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class EventControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function test_user_can_create_event()
  {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user);

    $eventData = [
      'name' => 'Gatekeeper Test Event',
      'event_date' => '2025-06-01',
      'start_date_time' => '2025-06-01 09:00:00',
      'end_date_time' => '2025-06-01 17:00:00',
      'location' => 'Test Venue TBA',
      'nonce_valid_for_minutes' => 15,
      'hodl_asset' => 1,
    ];

    $response = $this->post(route('manage-event.store'), $eventData);
    $response->assertStatus(302);
    $this->assertDatabaseHas('events', ['name' => 'Gatekeeper Test Event']);
  }

  /** @test */
  public function test_owner_can_view_manage_event_page()
  {
    $user = User::factory()->create();
    $event = Event::factory()->for($user)->create();
    $this->actingAs($user);

    $response = $this->get(route('manage-event.show', $event->uuid));

    $response->assertStatus(200);
    $response->assertInertia(fn(Assert $page) => $page
      ->component('ManageEvent/Show')
      ->has('event')
      ->where('event.uuid', $event->uuid));
  }
}
