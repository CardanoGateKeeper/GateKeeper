<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('events', static function (Blueprint $table) {
        $table->id();
        $table->timestamps();
        $table->uuid()
              ->unique();
        $table->foreignId('team_id')
              ->constrained();
        $table->foreignId('user_id')
              ->constrained();
        $table->string('name');
        $table->unsignedInteger('nonce_valid_for_minutes')
              ->default(15);
        $table->boolean('hodl_asset')
              ->default(false);
        $table->dateTime('start_date_time')
              ->nullable();
        $table->dateTime('end_date_time');
        $table->string('location')
              ->nullable();
        $table->string('event_start')
              ->nullable();
        $table->string('event_end')
              ->nullable();
        $table->date('event_date')
              ->nullable();
        $table->text('description')
              ->nullable();
        $table->string('bg_image_path')
              ->nullable();
        $table->string('profile_photo_path')
              ->nullable();
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('events');
    }
  };
