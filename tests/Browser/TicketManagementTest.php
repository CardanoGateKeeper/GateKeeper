<?php

// tests/Browser/TicketManagementTest.php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TicketManagementTest extends DuskTestCase
{
  public function testUserCanCreateTicket()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/events');

    });
  }

  public function testUserCanDeleteTicket()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/tickets');

    });
  }
}
