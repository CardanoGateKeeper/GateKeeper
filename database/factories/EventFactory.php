<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
  protected $model = Event::class;

  public function definition(): array
  {
    $start = Carbon::now()->addDays($this->faker->numberBetween(1, 30))->setTime(
      $this->faker->numberBetween(9, 18),
      $this->faker->randomElement([0, 15, 30, 45])
    );

    $end = (clone $start)->addHours($this->faker->numberBetween(1, 4));

    return [
      'uuid'                    => (string) Str::uuid(),
      'team_id'                 => Team::factory(),
      'user_id'                 => User::factory(),
      'name'                    => $this->faker->catchPhrase(),
      'nonce_valid_for_minutes' => 15,
      'hodl_asset'              => false,
      'start_date_time'         => $start,
      'end_date_time'           => $end,
      'location'                => $this->faker->city(),
      'event_start'             => $start->format('H:i'),
      'event_end'               => $end->format('H:i'),
      'event_date'              => $start->toDateString(),
      'description'             => $this->faker->paragraph(3),
      'bg_image_path'           => null,
      'profile_photo_path'      => null,
    ];
  }
}
