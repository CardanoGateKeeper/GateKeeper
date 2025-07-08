<?php

namespace Tests\Feature;
use App\Models\Policy;
use App\Models\Team;
use Tests\TestCase;

class PolicyControllerTest extends TestCase
{
  /** @test */
  public function index_returns_success()
  {
    $response = $this->get(route('policy.index'));

    $response->assertStatus(200);
  }

  /** @test */
  public function store_creates_policy()
  {
    $response = $this->post(route('policy.store'), [
      'name' => 'Test Policy',
    ]);

    $response->assertRedirect(route('policy.index'));
    $this->assertDatabaseHas('policies', ['name' => 'Test Policy']);
  }

  /** @test */
  public function show_returns_policy()
  {
    $policy = Policy::factory()->create();

    $response = $this->get(route('policy.show', $policy));

    $response->assertStatus(200);
    $response->assertViewHas('policy');
  }
}
