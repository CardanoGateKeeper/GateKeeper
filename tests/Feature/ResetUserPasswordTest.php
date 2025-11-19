<?php

namespace Tests\Feature;

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResetUserPasswordTest extends TestCase
{
  use RefreshDatabase;

  public function test_reset_password()
  {
    $user = User::factory()->create(['password' => Hash::make('oldpassword')]);

    $input = ['password' => 'newpassword123', 'password_confirmation' => 'newpassword123'];
    $action = new ResetUserPassword();
    $action->reset($user, $input);

    $this->assertTrue(Hash::check('newpassword123', $user->password));
  }
}
