<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Policy;
use App\Models\Team;
class TeamControllerTest extends TestCase
{
  /** @test */
  public function show_team_authorized()
  {
    $team = Team::factory()->create();

    $response = $this->get(route('teams.show', $team->id));

    $response->assertStatus(200);
    $response->assertSessionHas('flash.banner', "You're looking at a team pal!");
    $response->assertSee($team->name);
  }
}
