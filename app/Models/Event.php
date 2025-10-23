<?php

  namespace App\Models;

  use App\Traits\HasBackgroundImage;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Database\Eloquent\Relations\BelongsToMany;
  use Illuminate\Database\Eloquent\Relations\HasMany;
  use Illuminate\Database\Eloquent\Relations\HasManyThrough;
  use Illuminate\Support\Carbon;
  use Illuminate\Support\Collection;
  use Laravel\Jetstream\HasProfilePhoto;

  class Event extends Model
  {

    use HasFactory;
    use HasProfilePhoto;
    use HasBackgroundImage;

    protected $fillable = [
      'uuid',
      'team_id',
      'user_id',
      'name',
      'nonce_valid_for_minutes',
      'hodl_asset',
      'start_date_time',
      'end_date_time',
      'location',
      'event_start',
      'event_end',
      'event_date',
      'image',
    ];

    protected $dates = [
      'start_date_time',
      'end_date_time',
    ];

    protected $hidden = [
      'id',
      'team_id',
      'user_id',
      'created_at',
      'updated_at',
      'checkins',
      'checkouts'
    ];

    protected $appends = [
      'profile_photo_url',
      'bg_image_url',
    ];

    public function tickets(): HasMany
    {
      return $this->hasMany(Ticket::class);
    }

    public function checkins(): HasManyThrough
    {
      return $this->hasManyThrough(Checkin::class, Ticket::class);
    }

    public function checkouts(): HasManyThrough
    {
      return $this->hasManyThrough(Checkout::class, Ticket::class);
    }

    public function attendance(): Collection
    {
      return collect([
        ...$this->checkins,
        ...$this->checkouts
      ]);
//      return $this->checkins
//        ->merge($this->checkouts);
    }

    public function policies(): BelongsToMany
    {
      return $this->belongsToMany(Policy::class);
    }

    public function team(): BelongsTo
    {
      return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }

    public function description()
    {
      $description = "";
      if ($this->event_date) {
        $description .= date('l, F jS, Y', strtotime($this->event_date));
      }

      if ($this->event_start && $this->event_end) {
        $description .= " " . $this->event_start . " to " . $this->event_end . ".";
      } elseif ($this->event_start) {
        $description .= " " . $this->event_start;
      } elseif ($this->event_end) {
        $description .= " until " . $this->event_end;
      }

      if ($this->location) {
        $description .= " " . $this->location;
      }

      return $description;

    }

    public function isTicketingActive(): bool
    {
      $now = Carbon::now();

      if (!empty($this->start_date_time) && $now->isBefore($this->start_date_time)) {
        return false;
      }

      if ($now->isAfter($this->end_date_time)) {
        return false;
      }

      return true;
    }

    public function loadStats(): void
    {
      $stats = [
        'tickets' => [
          'count' => 0,
          'byDate' => [],
          'byPolicy' => []
        ],
        'attendance' => [
          'unique_checkin' => Ticket::where('event_id', $this->id)
                                    ->has('checkins')
                                    ->count(),
          'unique_checkout' => Ticket::where('event_id', $this->id)
                                     ->has('checkouts')
                                     ->count(),
          'byUser' => [],
          'byTime' => [
            'labels' => [],
            'datasets' => [
              [
                'label' => 'Attendance',
                'data' => []
              ]
            ]
          ]
        ],
      ];

      $event_tickets = Ticket::where('event_id', $this->id)
                             ->get();

      foreach ($event_tickets as $ticket) {
        $stats['tickets']['count']++;
        $ticket_policy = $this->policies()
                              ->where('id', $ticket->policy_id)
                              ->firstOrFail();

        @$stats['tickets']['byPolicy'][$ticket_policy->name]++;

        $ticket_created = Carbon::parse($ticket->created_at)
                                ->format('Y-m-d');

        @$stats['tickets']['byDate'][$ticket_created]++;
      }

      foreach ($this->team
                 ->allUsers() as $team_user) {
        @$stats['attendance']['byUser'][$team_user->name]['checkin'] = $this->checkins()
                                                                            ->where('user_id', $team_user->id)
                                                                            ->count();
        @$stats['attendance']['byUser'][$team_user->name]['checkout'] = $this->checkouts()
                                                                             ->where('user_id', $team_user->id)
                                                                             ->count();
      }

      $stats['tickets']['policy']['pieChart'] = [
        'labels' => array_keys($stats['tickets']['byPolicy']),
        'datasets' => [
          [
            'data' => array_values($stats['tickets']['byPolicy']),
          ]
        ]
      ];

      $this->checkins();
      $this->checkouts();

      $att = 0;
      $attendance = $this->attendance()
                         ->sortBy('created_at');
      foreach ($attendance as $entry) {
//        $stats['attendance']['byTime']['datasets'][0]['data'][] = $entry;

        $att += $entry->direction === 'in' ? 1 : -1;

        $stats['attendance']['byTime']['labels'][] = $entry->created_at->format('Y-m-d H:i');
        $stats['attendance']['byTime']['datasets'][0]['data'][] = $att;
      }

      $this->stats = $stats;

    }
  }
