<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EventCheckinControllerTest extends TestCase
{

  use RefreshDatabase;

  /** @test */
  public function index_displays_events_for_current_team()
  {
    $user = User::factory()->create();
    $team = Team::factory()->for($user, 'owner')->create();

    $events = Event::factory()
      ->count(3)
      ->for($team)
      ->for($user)
      ->create();

    $this->actingAs($user);

    $response = $this->get(route('scan-tickets.index'));
    $response->assertOk();
    $response->assertInertia(fn(Assert $page) => $page
      ->component('ScanTickets/Index')
      ->where('team.id', $team->id)
      ->where('team.name', $team->name)
      ->has('events', 3)
      ->has('events.0', fn(Assert $event) => $event
        ->hasAll([
          'uuid',
          'name',
          'event_date',
        ])
      )
    );
  }

  /** @test */
  public function show_displays_single_event()
  {
    $user = User::factory()->create();
    $team = Team::factory()->for($user, 'owner')->create();

    $events = Event::factory()
      ->count(3)
      ->for($team)
      ->for($user)
      ->create();

    $this->actingAs($user);

    $event = $events->random();

    $response = $this->get(route('scan-tickets.show', ['scan_ticket' => $event->uuid]));
    $response->assertOk();
    $response->assertInertia(fn(Assert $page) => $page
      ->component('ScanTickets/Show')
      ->has('event')
      ->where('event.uuid', $event->uuid)
    );
  }
}
