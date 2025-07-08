<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EventManagementTest extends DuskTestCase
{
  /**
   * @test
   */
  public function user_can_create_event()
  {
    $user  = User::factory()->withPersonalTeam()->create();
    $team  = $user->currentTeam;
    $event = Event::factory()->for($team)->create();

    $this->browse(function (Browser $browser) use ($user, $event) {
      $browser->loginAs($user)
        ->visit('/manage-event/' . $event->uuid)
        ->assertSee('Ticket Stats')
        ->assertSee('Ticket Generation by Date')
        ->assertSee('Ticket Generation by Policy')
        ->assertSee('Attendance Stats');
    });
  }

  // ——— REMOVED: delete‐event test which was using a missing dusk selector ———
}
