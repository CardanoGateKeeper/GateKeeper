<?php

namespace Tests\Feature;

use App\Models\Policy;
use App\Models\Team;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventPolicyControllerTest extends TestCase
{
  use RefreshDatabase;

  private function setup_test()
  {
    $this->user = User::factory()->withPersonalTeam()->create();
    $this->event = Event::factory(['user_id' => $this->user->id, 'team_id' => $this->user->currentTeam->id])->create();
    $this->policy = Policy::factory(['user_id' => $this->user->id, 'team_id' => $this->user->currentTeam->id])->create();
  }

  /** @test */
  public function it_can_attach_policy_to_event()
  {
    $this->setup_test();

    $this->actingAs($this->user);

    $response = $this->post(route('event.policy.store', [$this->event->uuid]), ['policy_id' => $this->policy->id]);

    $response->assertStatus(303);
  }

  /** @test */
  public function it_can_detach_policy_from_event()
  {
    $this->setup_test();

    $this->actingAs($this->user);

    $response = $this->delete(route('event.policy.destroy', [$this->event->uuid, $this->policy->id]));

    $response->assertStatus(303);

  }
}
