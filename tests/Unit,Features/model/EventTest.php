<?php

namespace Tests\Unit\Model;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function event_is_ticketing_active()
  {
    $event = Event::factory()->create(['event_date' => now()->addDays(1)]);

    $this->assertTrue($event->isTicketingActive());

    $event->event_date = now()->subDays(1);

    $this->assertFalse($event->isTicketingActive());
  }
}
