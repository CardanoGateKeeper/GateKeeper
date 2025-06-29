<?php

namespace Tests\Feature;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Policy;

class TeamPolicyControllerTest extends TestCase
{
  use RefreshDatabase;

  public function test_store_creates_policy_for_team()
  {
    $team = Team::factory()->create();

    $data = [
      'name' => 'Test Policy',
      'description' => 'Sample description',
      // Add all required fields of StorePolicyRequest validation here
    ];

    $response = $this->post(route('teams.policies.store', $team), $data);

    $response->assertStatus(303);
    $this->assertDatabaseHas('policies', [
      'team_id' => $team->id,
      'name' => $data['name'],
    ]);
  }
}
