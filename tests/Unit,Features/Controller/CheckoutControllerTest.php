<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
  /** @test */
  public function checkout_ticket()
  {
    $response = $this->post(route('event.check-out.store', ['event' => 1]), [
      'ticket_id' => 1,
      'user_id' => 1,
    ]);

    $response->assertStatus(302);
  }

  /** @test */
  public function checkout_ticket_not_checked_in()
  {
    $response = $this->post(route('event.check-out.store', ['event' => 1]), [
      'ticket_id' => 1,
      'user_id' => 1,
    ]);

    $response->assertStatus(400); // Assuming 400 for non-checked-in ticket
  }
}
