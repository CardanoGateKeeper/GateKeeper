<?php

use App\Models\Event;
use App\Models\Policy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Event::class);
            $table->foreignIdFor(Policy::class);
            $table->string('asset_id', 64);
            $table->string('stake_key', 64);
            $table->char('signature_nonce', 16)->charset('binary')->unique();
            $table->char('ticket_nonce', 16)->charset('binary')->nullable()->unique();
            $table->longText('signature')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
