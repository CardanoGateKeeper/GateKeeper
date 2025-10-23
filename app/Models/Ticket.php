<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Database\Eloquent\Relations\HasMany;
  use Ramsey\Uuid\Uuid;

  class Ticket extends Model
  {

    use HasFactory;

    const TICKET_TYPE = 'GateKeeperTicket';
    const TICKET_VERSION = '1.0.0';
    const ATOM_DATE_TIME_FORMAT = 'Y-m-d\TH:i:sP';

    protected $fillable = [
      'event_id',
      'policy_id',
      'asset_id',
      'stake_key',
      'signature_nonce',
      'ticket_nonce',
      'is_checked_in',
      'signature',
      'check_in_time',
      'check_in_user',
    ];


    protected $dates = [
      'check_in_time',
    ];

    public function event(): BelongsTo
    {
      return $this->belongsTo(Event::class);
    }

    public function policy(): BelongsTo
    {
      return $this->belongsTo(Policy::class);
    }

    public function checkins(): HasMany
    {
      return $this->hasMany(Checkin::class);
    }

    public function checkouts(): HasMany
    {
      return $this->hasMany(Checkout::class);
    }

    public function expires(): string
    {
      return $this->created_at->clone()
                              ->addMinutes($this->event->nonce_valid_for_minutes)
                              ->format(self::ATOM_DATE_TIME_FORMAT);
    }

    public function generate_signing_json(): string
    {

      return json_encode([
        'assetId' => $this->asset_id,
        'eventId' => $this->event->uuid,
        'eventName' => $this->event->name,
        'policyId' => $this->policy->hash,
        'signBy' => $this->expires(),
        'stakeKey' => $this->stake_key,
        'ticketId' => Uuid::fromBytes($this->signature_nonce)
                          ->toString(),
        'type' => self::TICKET_TYPE,
        'version' => self::TICKET_VERSION,
      ], JSON_THROW_ON_ERROR);

    }

    /**
     * When we mark a new ticket as valid, we need to delete all old/previous
     * versions of the ticket. This will have the effect of invalidating them.
     *
     * @return void
     */
    public function removeOldAttempts(): void
    {
      Ticket::query()
            ->where('event_id', $this->event_id)
            ->where('policy_id', $this->policy_id)
            ->where('asset_id', $this->asset_id)
            ->where('id', '<>', $this->id)
            ->delete();
    }

  }
