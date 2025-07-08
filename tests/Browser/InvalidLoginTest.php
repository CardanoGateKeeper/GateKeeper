<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InvalidLoginTest extends DuskTestCase
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

  public function testShowsErrorOnBadCredentials()
  {
    $this->browse(function (Browser $browser) {
      $browser->visit('/login')
        ->pause(1000) // Let Vue/Vuetify hydrate
        ->typeSlowly('#input-0', 'user@example.com')
        ->typeSlowly('#input-2', 'wrongpassword')
        ->pause(500)
        ->click('button[type="submit"]:not([disabled])')
        ->pause(1000)
        ->screenshot('invalid-login')
        ->screenshot('checkin-debug')
        ->assertSee('These credentials do not match our records');
    });
  }
}
