<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('policies', static function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('hash', 56);
            $table->string('name');
            $table->foreignId('team_id')
                  ->constrained();
            $table->foreignId('user_id')
                  ->constrained();
            $table->unique(['team_id','hash'], 'UK_TEAM_POLICY_HASH');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('policies');
    }
};
