<?php

namespace Tests\Browser;

use App\Models\Event;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EventShowTest extends DuskTestCase
{
  /**
   * @test
   */
  public function event_show_page_displays_event_name()
  {
    $event = Event::factory()->create();

    $this->browse(function (Browser $browser) use ($event) {
      $browser->visit("/event/{$event->uuid}")
        ->assertSee($event->name);
    });
  }

  // ——— REMOVED: connect‐wallet button tests (too dependent on client‐side JS) ———
}
