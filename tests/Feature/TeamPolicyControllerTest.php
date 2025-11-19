<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamPolicyControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function test_store_creates_policy_for_team()
  {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    $this->actingAs($user);

    $data = [
      'name' => 'Team Policy Test Policy',
      'hash' => bin2hex(random_bytes(28)),
      'team_id' => $team->id,
      'user_id' => $team->user_id,
    ];

    $response = $this->post(route('team.policy.store', $team), $data);
    $response->assertStatus(303);
    $this->assertDatabaseHas('policies', [
      'team_id' => $team->id,
      'name' => $data['name'],
    ]);
  }
}
