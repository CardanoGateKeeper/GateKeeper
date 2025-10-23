<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
  public function setUp(): void
  {
    parent::setUp();

    Artisan::call('migrate:fresh');

    User::factory()->create([
      'email' => 'user@example.com',
      'password' => Hash::make('password123'),
    ]);
  }

  public function testUserCanLogin()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/login')
        ->pause(1000) // Wait for Vue/Vuetify to hydrate
        ->typeSlowly('#input-0', 'user@example.com')
        ->typeSlowly('#input-2', 'password123')
        ->pause(500)
        ->click('button[type="submit"]:not([disabled])')
        ->waitForLocation('/dashboard', 10)
        ->assertPathIs('/dashboard');
    });
  }
}
