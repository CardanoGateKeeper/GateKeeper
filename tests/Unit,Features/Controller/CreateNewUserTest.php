<?php

namespace Tests\Unit\Controller;

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Policy;

class CreateNewUserTest extends TestCase
{
  use RefreshDatabase;

  public function test_create_user()
  {
    $input = [
      'name' => 'John Doe',
      'email' => 'john@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123',
      'terms' => true
    ];

    $action = new CreateNewUser();
    $user = $action->create($input);

    $this->assertInstanceOf(User::class, $user);
    $this->assertTrue(Hash::check('password123', $user->password));
  }
}
