<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Policy;

class EventAdminControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function user_can_store_new_event()
  {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user);

    $payload = [
      'uuid' => (string) \Str::uuid(),
      'team_id' => $user->currentTeam->id,
      'user_id' => $user->id,
      'name' => 'Test Event',
      'nonce_valid_for_minutes' => 30,
      'hodl_asset' => false,
      'start_date_time' => now()->subHour()->toDateTimeString(),
      'end_date_time' => now()->addHour()->toDateTimeString(),
      'event_date' => now()->toDateString(),
      'location' => 'Test City',
      'event_start' => '10:00',
      'event_end' => '12:00',
    ];

    $response = $this->post(route('manage-event.store'), $payload);
    $response->assertRedirect();

    $this->assertDatabaseHas('events', [
      'name' => 'Test Event',
      'team_id' => $user->currentTeam->id,
    ]);
  }

  /** @test */
  public function user_can_update_event()
  {
    $user = User::factory()->withPersonalTeam()->create();
    $this->actingAs($user);

    $event = Event::factory()->create([
      'team_id' => $user->currentTeam->id,
      'user_id' => $user->id,
    ]);

    $payload = [
      'name' => 'Updated Event Name',
      'nonce_valid_for_minutes' => 45,
      'hodl_asset' => true,
      'event_date' => now()->toDateString(),
      'start_date_time' => now()->subHours(2)->toDateTimeString(),
      'end_date_time' => now()->addHours(2)->toDateTimeString(),
      'location' => 'Updated City',
      'event_start' => '11:00',
      'event_end' => '13:00',
    ];


    $response = $this->put(route('manage-event.update', $event->uuid), $payload);
    $response->assertRedirect(route('manage-event.edit', $event->uuid));

    $this->assertDatabaseHas('events', [
      'uuid' => $event->uuid,
      'name' => 'Updated Event Name',
    ]);
  }
}
