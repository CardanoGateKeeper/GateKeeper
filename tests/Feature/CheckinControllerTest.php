<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;

class CheckinControllerTest extends TestCase
{
  use RefreshDatabase;

  protected function setup_test(): void
  {
    $this->user = User::factory()->create();
    $this->event = Event::factory()->create();
    $this->ticket = Ticket::factory()->create([
      'event_id' => $this->event->id
    ]);
  }

  protected function doCheckIn()
  {
    return $this->actingAs($this->user)->post(route('event.check-in.store', ['event' => $this->event->uuid]), [
      'asset_id' => $this->ticket->asset_id,
      'ticket_code' => Uuid::fromBytes($this->ticket->ticket_nonce)->toString(),
    ]);
  }

  /** @test */
  public function test_can_checkin_ticket()
  {
    $this->setup_test();
    $response = $this->doCheckIn();

    $response->assertStatus(200);
  }

  /** @test */
  public function test_checkin_fails_if_ticket_already_checked_in()
  {
    $this->setup_test();
    $response = $this->doCheckIn();

    $response->assertStatus(200);

    $response = $this->doCheckIn();

    // Ensure we get a 400 response because the ticket is already checked in
    $response->assertStatus(400);  // 400 is typically used for invalid requests
    $response->assertJson(['message' => 'Ticket has already been checked in!']);
  }

}
