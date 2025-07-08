<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LogoutTest extends DuskTestCase
{
  public function test_user_can_logout_from_dashboard()
  {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
      $browser->loginAs($user)
        ->visit('/dashboard')
        ->pause(1000) // Let everything render
        ->assertSee('Dashboard') // Confirm dashboard loaded
        ->click('@user-dropdown') // Open dropdown (use Dusk selector if available)
        ->pause(500)
        ->click('@logout-button') // Click logout (Dusk selector preferred)
        ->waitForLocation('/login', 10)
        ->assertPathIs('/login')
        ->assertSee('Log in');
    });
  }
}
