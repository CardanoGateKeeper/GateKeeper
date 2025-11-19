<?php

namespace Tests\Unit\Model;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function event_ticketing_active_when_start_date_time_less_than_now()
  {
    $event = Event::factory()->create(['start_date_time' => now()->subDays(1), 'end_date_time' => now()->addDays(1)]);
    $this->assertTrue($event->isTicketingActive());
    $event->start_date_time = now()->addDays(1);
    $this->assertFalse($event->isTicketingActive());
  }

  /** @test */
  public function event_ticketing_active_when_end_date_greater_than_now()
  {
    $event = Event::factory()->create(['start_date_time' => now()->subDays(2), 'end_date_time' => now()->addDays(1)]);
    $this->assertTrue($event->isTicketingActive());
    $event->end_date_time = now()->subDays(1);
    $this->assertFalse($event->isTicketingActive());
  }
}
