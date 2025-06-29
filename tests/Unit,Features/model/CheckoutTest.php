<?php

namespace Tests\Unit\Model;

use App\Models\Checkout;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function test_checkout_belongs_to_ticket()
  {
    $checkout = Checkout::factory()->create();
    $this->assertInstanceOf(Ticket::class, $checkout->ticket);
  }

  /** @test */
  public function test_checkout_belongs_to_user()
  {
    $checkout = Checkout::factory()->create();
    $this->assertInstanceOf(User::class, $checkout->user);
  }

  /** @test */
  public function test_checkout_direction_is_out()
  {
    $checkout = Checkout::factory()->create();
    $this->assertEquals('out', $checkout->direction);
  }
}
