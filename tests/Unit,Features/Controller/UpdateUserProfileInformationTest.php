<?php

namespace Tests\Unit\Controller;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateUserProfileInformationTest extends TestCase
{
  use RefreshDatabase;

  public function test_update_profile_information()
  {
    $user = User::factory()->create();
    $input = ['name' => 'Jane Doe', 'email' => 'jane@example.com'];

    $action = new UpdateUserProfileInformation();
    $action->update($user, $input);

    $user->refresh();

    $this->assertEquals('Jane Doe', $user->name);
    $this->assertEquals('jane@example.com', $user->email);
  }
}
