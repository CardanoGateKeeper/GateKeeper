<?php

// tests/Browser/DashboardNavigationTest.php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DashboardNavigationTest extends DuskTestCase
{
  public function testDashboardNavigation()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/dashboard');
    });
  }

  public function testEventsNavigation()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/dashboard');

    });
  }
}
