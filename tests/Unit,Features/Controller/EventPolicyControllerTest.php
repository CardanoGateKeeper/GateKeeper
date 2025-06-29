<?php

namespace Tests\Feature;
use App\Models\Policy;
use App\Models\Team;
use App\Models\Event;
use Tests\TestCase;

class EventPolicyControllerTest extends TestCase
{
  /** @test */
  public function it_can_attach_policy_to_event()
  {
    $event = Event::factory()->create();
    $policy = Policy::factory()->create();

    $response = $this->post(route('event.policy.store', [$event, $policy]));

    $response->assertRedirect(route('event.show', $event));
  }

  /** @test */
  public function it_can_detach_policy_from_event()
  {
    $event = Event::factory()->create();
    $policy = Policy::factory()->create();

    $response = $this->delete(route('event.policy.destroy', [$event, $policy]));

    $response->assertRedirect(route('event.show', $event));
  }
}
