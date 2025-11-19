<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{

  use HasFactory;
  use HasProfilePhoto;

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'personal_team' => 'boolean',
  ];

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'personal_team',
  ];

  protected $hidden = [
    'created_at',
    'updated_at',
  ];

  /**
   * The event map for the model.
   *
   * @var array<string, class-string>
   */
  protected $dispatchesEvents = [
    'created' => TeamCreated::class,
    'updated' => TeamUpdated::class,
    'deleted' => TeamDeleted::class,
  ];

  protected $appends = [
    'profile_photo_url',
  ];

  public function policies(): HasMany
  {
    return $this->hasMany(Policy::class);
  }

  public function events(): HasMany
  {
    return $this->hasMany(Event::class);
  }

  public function owner(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}
