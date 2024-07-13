<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsToMany;
  use Laravel\Jetstream\HasProfilePhoto;

  class Policy extends Model
  {

    use HasFactory, HasProfilePhoto;

    protected $fillable = [
      'hash',
      'name',
      'team_id',
      'user_id',
    ];

    protected $hidden = [
      'created_at',
      'updated_at',
    ];

    protected $appends = [
      'profile_photo_url',
    ];

    public function events(): BelongsToMany
    {
      return $this->belongsToMany(Event::class);
    }

  }
