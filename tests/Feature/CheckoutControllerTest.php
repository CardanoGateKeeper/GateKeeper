<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
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

  protected function doCheckOut()
  {
    return $this->actingAs($this->user)->post(route('event.check-out.store', ['event' => $this->event->uuid]), [
      'asset_id' => $this->ticket->asset_id,
      'ticket_code' => Uuid::fromBytes($this->ticket->ticket_nonce)->toString(),
    ]);
  }

  /** @test */
  public function cannot_checkout_ticket_that_is_not_checked_in()
  {
    $this->setup_test();
    $response = $this->doCheckOut();

//    dd($response);

    $response->assertStatus(400);
    $response->assertJson(['message' => 'Ticket has not been checked in!']);
  }

  /** @test */
  public function can_checkout_ticket_if_checked_in()
  {
    $this->setup_test();
    $response = $this->doCheckIn();

    $response->assertStatus(200);

    $response = $this->doCheckOut();

    $response->assertStatus(200);
  }

}
