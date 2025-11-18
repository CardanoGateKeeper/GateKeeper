<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\Event;
use App\Models\Policy;
use App\Models\Ticket;
use App\Models\Checkin;
use App\Models\Checkout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class DemoEventSeeder extends Seeder
{
  public function run(): void
  {
    // 1) Create a demo user you can log in with
    $user = User::query()->first() ?? User::factory()->create([
      'name' => 'Demo Organizer',
      'email' => 'demo@example.com',
      'password' => Hash::make('password'), // <- demo password
    ]);

    // 2) Create a team for that user (or reuse first team)
    $team = Team::query()->first() ?? Team::factory()->create([
      'user_id' => $user->id,
      'name' => 'Demo Event Team',
      'personal_team' => true,
    ]);

    // 3) Well-known Cardano policies we want to use in the demo
    $knownPolicies = [
      [
        'name' => 'HOSKY Token',
        'hash' => 'a0028f350aaabe0545fdcb56b039bfb08e4bb4d8c4d7c3c7d481c235',
      ],
      [
        'name' => 'Clay Nation (NFT)',
        'hash' => '40fa2aa67258b4ce7b5782f74831d46a84c59a0ff0c28262fab21728',
      ],
      [
        'name' => 'SpaceBudz (NFT)',
        'hash' => 'f0ff48bbb7bbe9d59a40f1ce90e9e9d0ff5002ec48f232b49ca0fb9a',
      ],
      [
        'name' => 'World Mobile Token (WMT)',
        'hash' => '1d7f33bd23d85e1a25d87d86fac4f199c3197a2f7afeb662a0f34e1e',
      ],
      [
        'name' => 'CLAY (Clay Nation FT)',
        'hash' => '38ad9dc3aec6a2f38e220142b9aa6ade63ebe71f65e7cc2b7d8a8535',
      ],
    ];

    // 4) Seed (or re-use) those policies for this team/user
    $policies = collect($knownPolicies)->map(function (array $p) use ($team, $user) {
      return Policy::updateOrCreate(
        [
          'team_id' => $team->id,
          'hash' => $p['hash'],
        ],
        [
          'name' => $p['name'],
          'user_id' => $user->id,
        ]
      );
    });

    // 4) Create some demo events for this team/user
    $eventDefinitions = [
      [
        'name' => 'Hydra Launch Party',
        'description' => 'An exclusive event showcasing the Hydra-powered ticketing system.',
        'days_from' => 10,
        'hours_long' => 3,
        'location' => 'Las Vegas Convention Center',
        'ticket_window' => 'future',   // ticketing not yet started
      ],
      [
        'name' => 'Cardano Community Meetup',
        'description' => 'Monthly meetup for Cardano builders and enthusiasts.',
        'days_from' => 3,
        'hours_long' => 2,
        'location' => 'Phoenix Blockchain Hub',
        'ticket_window' => 'current',  // ticketing currently open
      ],
      [
        'name' => 'VIP Governance Summit',
        'description' => 'Invite-only strategy session for governance and scaling.',
        'days_from' => 21,
        'hours_long' => 4,
        'location' => 'Berlin Innovation Campus',
        'ticket_window' => 'closed',   // ticketing already closed
      ],
    ];

    $events = collect($eventDefinitions)->map(function (array $spec) use ($team, $user) {
      $eventStart = Carbon::now()
        ->addDays($spec['days_from'])
        ->setTime(18, 0); // 6pm for all demo events

      $eventEnd = (clone $eventStart)->addHours($spec['hours_long']);

      $now = Carbon::now();
      switch ($spec['ticket_window']) {
        case 'future':
          // Ticketing opens in the future (not yet active)
          $ticketStart = $now->copy()->addDays(2)->setTime(9, 0);
          $ticketEnd = $now->copy()->addDays(5)->setTime(21, 0);
          break;

        case 'closed':
          // Ticketing already ended in the past
          $ticketStart = $now->copy()->subDays(10)->setTime(9, 0);
          $ticketEnd = $now->copy()->subDays(5)->setTime(21, 0);
          break;

        case 'current':
        default:
          // Ticketing currently open (now is between start and end)
          $ticketStart = $now->copy()->subDays(1)->setTime(9, 0);
          $ticketEnd = $now->copy()->addDays(2)->setTime(21, 0);
          break;
      }

      return Event::factory()
        ->for($team)
        ->for($user)
        ->create([
          'name' => $spec['name'],
          'description' => $spec['description'],
          'location' => $spec['location'],
          'start_date_time' => $ticketStart,
          'end_date_time' => $ticketEnd,
          'event_start' => $eventStart->format('H:i'),
          'event_end' => $eventEnd->format('H:i'),
          'event_date' => $eventStart->toDateString(),
          'is_public' => true,
        ]);
    });

    // 5) Attach policies to events via pivot
    $events->each(function (Event $event) use ($policies) {
      $event->policies()->sync(
        $policies->random($policies->count() - 1)->pluck('id')->all()
      );
    });

    // 6) Create tickets per event
    $events->each(function (Event $event) use ($policies) {
      if ($event->policies()->count() === 0) {
        $event->policies()->attach(
          $policies->random(2)->pluck('id')->all()
        );
        $event->refresh();
      }

      $eventPolicies = $event->policies;

      // 10 tickets per event, spread across policies
      Ticket::factory()
        ->count(10)
        ->for($event)
        ->state(function () use ($eventPolicies) {
          return [
            'policy_id' => $eventPolicies->random()->id,
          ];
        })
        ->create();
    });

    // 7) Create past events with simulated tickets & check-ins
    $pastEventDefinitions = [
      [
        'name' => 'Hydra Dev Workshop (Past)',
        'description' => 'Hands-on workshop that already happened.',
        'days_ago' => 7,
        'hours_long' => 3,
        'location' => 'Berlin',
      ],
      [
        'name' => 'Governance Roundtable (Past)',
        'description' => 'Past governance roundtable with real attendance.',
        'days_ago' => 30,
        'hours_long' => 4,
        'location' => 'Online',
      ],
    ];

    $pastEvents = collect($pastEventDefinitions)->map(function (array $spec) use ($team, $user) {
      $eventStart = Carbon::now()
        ->subDays($spec['days_ago'])
        ->setTime(16, 0);

      $eventEnd = (clone $eventStart)->addHours($spec['hours_long']);

      // Ticketing window entirely in the past
      $ticketStart = (clone $eventStart)->subDays(2);
      $ticketEnd = (clone $eventStart)->subHours(1);

      return Event::factory()
        ->for($team)
        ->for($user)
        ->create([
          'name' => $spec['name'],
          'description' => $spec['description'],
          'location' => $spec['location'],
          'start_date_time' => $ticketStart,
          'end_date_time' => $ticketEnd,
          'event_start' => $eventStart->format('H:i'),
          'event_end' => $eventEnd->format('H:i'),
          'event_date' => $eventStart->toDateString(),
          'is_public' => true,
        ]);
    });

    // Attach policies to past events
    $pastEvents->each(function (Event $event) use ($policies) {
      $event->policies()->sync(
        $policies->random(2)->pluck('id')->all()
      );
    });

    // Create tickets + check-ins/check-outs for past events
    $pastEvents->each(function (Event $event) use ($policies, $user) {
      $eventPolicies = $event->policies;

      $tickets = Ticket::factory()
        ->count(15)
        ->for($event)
        ->state(function () use ($eventPolicies) {
          return [
            'policy_id' => $eventPolicies->random()->id,
          ];
        })
        ->create();

      // ~70% of ticket holders check in, ~80% of those check out
      foreach ($tickets as $ticket) {
        if (rand(1, 100) <= 70) {
          $checkinTime = $event->event_date
            ->copy()
            ->setTimeFromTimeString($event->event_start) // "16:00" → time on that date
            ->addMinutes(rand(-30, 60));

          $checkin = Checkin::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'created_at' => $checkinTime,
            'updated_at' => $checkinTime,
          ]);

          if (rand(1, 100) <= 80) {
            $checkoutTime = $checkinTime->copy()->addMinutes(rand(30, 180));

            Checkout::create([
              'ticket_id' => $ticket->id,
              'user_id' => $user->id,
              'created_at' => $checkoutTime,
              'updated_at' => $checkoutTime,
            ]);
          }
        }
      }
    });

    $this->command?->info('Demo events, policies, and tickets seeded.');
    $this->command?->info('Demo login: demo@example.com / password');
  }
}
