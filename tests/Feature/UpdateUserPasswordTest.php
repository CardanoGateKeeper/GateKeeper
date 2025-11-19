<?php

namespace Tests\Feature;

use App\Actions\Fortify\UpdateUserPassword;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateUserPasswordTest extends TestCase
{
  /** @test */
  public function can_update_user_password()
  {
    $user = User::factory()->create();

    $user->password = Hash::make('password');
    $user->save();
    $user->refresh();

    $this->assertTrue(
      Hash::check('password', $user->password),
      'Sanity check failed: stored password is not "password".'
    );

    $input = [
      'current_password' => 'password',
      'password' => 'this is my password!',
      'password_confirmation' => 'this is my password!',
    ];

    $this->actingAs($user, 'web');

    $action = new UpdateUserPassword();
    $action->update($user, $input);

    $user->refresh();

    $this->assertTrue(
      Hash::check($input['password'], $user->password),
      'New password was not saved correctly.'
    );
  }
}
