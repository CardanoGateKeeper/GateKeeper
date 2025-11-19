<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;
use App\Models\Team;

class TeamControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function show_team_authorized()
  {

    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $this->actingAs($user);

    $response = $this->get(route('teams.show', $team->id));
    $response->assertOk();
    $response->assertInertia(fn(Assert $page) => $page
    ->has('team')
    ->where('team.user_id', $user->id));

    /*dd($response);

    $response->assertStatus(200);
    $response->assertSessionHas('flash.banner', "You're looking at a team pal!");
    $response->assertSee($team->name);*/
  }
}
