<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProfileManagementTest extends DuskTestCase
{
  public function test_user_can_view_profile_page()
  {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
      $browser->loginAs($user)
        ->visit('/user/profile')
        ->pause(1000)
        ->assertSee('Profile'); // Or whatever heading text appears on the page
    });
  }
}
