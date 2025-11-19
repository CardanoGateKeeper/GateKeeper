<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Rules\Password as PasswordRule; // Fortify’s rule
use App\Models\User;

class UpdateUserPasswordTest
{
  /**
   * Validate and update the given user's password.
   */
  public function update(User $user, array $input): void
  {
    Validator::make($input, [
      'current_password' => ['required', 'string'],
      'password'         => ['required', 'string', new PasswordRule, 'confirmed'],
    ])->after(function ($validator) use ($user, $input) {
      if (! Hash::check($input['current_password'], $user->password)) {
        $validator->errors()->add(
          'current_password',
          __('The provided password does not match your current password.')
        );
      }
    })->validate();

    $user->forceFill([
      'password' => Hash::make($input['password']),
    ])->save();
  }
}
