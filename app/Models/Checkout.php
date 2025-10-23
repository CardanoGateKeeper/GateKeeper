<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;

  class Checkout extends Model
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

    public function getDirectionAttribute()
    {
      return 'out';
    }
  }
