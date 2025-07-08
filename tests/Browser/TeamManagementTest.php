<?php

// tests/Browser/TeamManagementTest.php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TeamManagementTest extends DuskTestCase
{
  public function testUserCanCreateNewTeam()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/dashboard');

    });
  }

  public function testUserCanEditTeamSettings()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/team-settings');
    });
  }
}
