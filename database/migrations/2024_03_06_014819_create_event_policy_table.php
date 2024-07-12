<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('event_policy', static function (Blueprint $table) {
            $table->foreignId('event_id')
                  ->constrained();
            $table->foreignId('policy_id')
                  ->constrained();
            $table->unique([
                'event_id',
                'policy_id',
            ], 'UK_EVENT_POLICY');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('event_policy');
    }
};
