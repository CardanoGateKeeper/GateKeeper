<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Database\Eloquent\Relations\HasOneThrough;

  class Checkin extends Model
  {
    use HasFactory;

    protected $appends = ['direction'];

    public function ticket(): BelongsTo
    {
      return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
      return $this->belongsTo(User::class);
    }

    public function event(): HasOneThrough
    {
      return $this->hasOneThrough(Event::class, Ticket::class);
    }

    public function getDirectionAttribute()
    {
      return 'in';
    }
  }
