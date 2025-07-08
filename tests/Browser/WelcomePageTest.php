<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WelcomePageTest extends DuskTestCase
{
  public function testWelcomePageLinks()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/')
        ->assertSeeLink('Log In')
        ->assertSeeLink('Register');
    });
  }

  public function testNavigationToLogin()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/')
        ->clickLink('Log In')
        ->waitForLocation('/login')
        ->assertPathIs('/login');
    });
  }

  public function testNavigationToRegister()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/')
        ->clickLink('Register')
        ->waitForLocation('/register')
        ->assertPathIs('/register');
    });
  }
}
