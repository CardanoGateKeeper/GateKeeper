<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Policy;

class TicketControllerTest extends TestCase
{
  /** @test */
  public function store_creates_ticket_and_returns_nonce()
  {
    $response = $this->post(route('ticket.store'), [
      'asset_id' => 'asset123',
      'stake_key' => 'stake123',
    ]);

    $response->assertStatus(201);
    $response->assertJsonStructure(['nonce']);
  }

  /** @test */
  public function update_validates_signature_and_returns_qr_value()
  {
    $response = $this->put(route('ticket.update', ['ticket' => 1]), [
      'signature' => 'valid-signature',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['qr']);
  }
}
