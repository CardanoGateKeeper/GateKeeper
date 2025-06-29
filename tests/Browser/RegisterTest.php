<?php

namespace Tests\Browser;

use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
  public function setUp(): void
  {
    parent::setUp();

    // Fresh-migrate the database for each Dusk test
    Artisan::call('migrate:fresh');
  }

  public function testUserCanRegister()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/register')
        ->type('#input-0', 'Test User')           // Vuetify name field
        ->type('#input-2', 'user@example.com')    // Vuetify email field
        ->type('#input-4', 'password123')         // Vuetify password field
        ->type('#input-6', 'password123')         // Vuetify password confirmation
        ->click('#switch-8')                      // accept terms switch
        ->pause(500)                              // wait for button to enable
        ->click('button[type="submit"]')          // click submit
        ->waitForLocation('/dashboard')           // wait for redirect
        ->assertPathIs('/dashboard')              // assert location
        ->screenshot('after-register');           // ← save screenshot
    });
  }
}
