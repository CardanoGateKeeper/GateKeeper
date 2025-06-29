<?php

namespace Tests\Unit\Model;

use App\Models\Checkin;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckinTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function test_checkin_belongs_to_ticket()
  {
    $checkin = Checkin::factory()->create();
    $this->assertInstanceOf(Ticket::class, $checkin->ticket);
  }

  /** @test */
  public function test_checkin_belongs_to_user()
  {
    $checkin = Checkin::factory()->create();
    $this->assertInstanceOf(User::class, $checkin->user);
  }

  /** @test */
  public function test_checkin_direction_is_in()
  {
    $checkin = Checkin::factory()->create();
    $this->assertEquals('in', $checkin->direction);
  }
}
