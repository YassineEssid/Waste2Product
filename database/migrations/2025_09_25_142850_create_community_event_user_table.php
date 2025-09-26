<?php

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
        Schema::create('community_event_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('registered_at')->useCurrent();
            $table->enum('status', ['registered', 'attended', 'cancelled'])->default('registered');
            $table->timestamps();
            
            // Prevent duplicate registrations
            $table->unique(['community_event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_event_user');
    }
};
