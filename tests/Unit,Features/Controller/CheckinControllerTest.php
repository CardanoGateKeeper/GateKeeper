<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Symfony\Component\Routing\Annotation\Route;
use Tests\TestCase;
use App\Models\Policy;

class CheckinControllerTest extends TestCase
{
  /** @test */
  public function test_checkin_ticket()
  {

    $response = $this->post(route('event.check-in.store', ['event' => 1]), [
      'ticket_id' => 1,
      'user_id' => 1,
    ]);

    $response->assertStatus(302);
  }

  /** @test */
  public function test_checkin_ticket_already_checked_in()
  {
    // Create a test event and ticket
    $event = Event::factory()->create();
    $ticket = Ticket::factory()->create([
      'event_id' => $event->id,
      'status' => 'checked_in',
    ]);

    // Make the POST request to the check-in route
    $response = $this->post(route('event.check-in.store', ['event' => $event->uuid]), [
      'ticket_code' => $ticket->ticket_nonce,
      'asset_id' => $ticket->asset_id,
    ]);

    // Ensure we get a 400 response because the ticket is already checked in
    $response->assertStatus(400);  // 400 is typically used for invalid requests
    $response->assertJson(['message' => 'Ticket has already been checked in!']);
  }

}
