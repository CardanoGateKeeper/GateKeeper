<?php

namespace Database\Factories;

use App\Models\Policy;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Policy>
 */
class PolicyFactory extends Factory
{
  protected $model = Policy::class;

  public function definition(): array
  {
    return [
      'hash' => Str::random(56),
      'name' => $this->faker->words(3, true) . ' Policy',
      'team_id' => Team::factory(),
      'user_id' => User::factory(),
    ];
  }
}
