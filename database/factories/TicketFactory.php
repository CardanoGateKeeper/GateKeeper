<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Event;
use App\Models\Policy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
  protected $model = Ticket::class;

  public function definition(): array
  {
    return [
      'event_id' => Event::factory(),
      'policy_id' => Policy::factory(),
      'asset_id' => $this->faker->bothify(str_repeat('#', 40)), // adjust length as needed
      'stake_key' => $this->faker->bothify(str_repeat('#', 40)),
      'signature_nonce' => random_bytes(16), // binary(16)
      'ticket_nonce' => random_bytes(16), // binary(16), nullable in schema but we'll fill it
      'signature' => null,
    ];
  }
}
