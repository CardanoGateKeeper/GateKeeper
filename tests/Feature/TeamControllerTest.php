<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TeamControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function show_team_authorized()
  {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $this->actingAs($user);

//    $this->withoutExceptionHandling();

    $response = $this->get(route('teams.show', $team));

//    $response->dump();

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
      ->has('team')
      ->where('team.user_id', $user->id)
    );
  }
}
