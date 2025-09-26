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
        Schema::create('community_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // event creator
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['workshop', 'webinar', 'exchange', 'meetup', 'cleanup', 'other'])->default('workshop');
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->text('location_address')->nullable();
            $table->decimal('location_lat', 10, 8)->nullable();
            $table->decimal('location_lng', 11, 8)->nullable();
            $table->boolean('is_online')->default(false);
            $table->string('meeting_link')->nullable();
            $table->integer('max_participants')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->json('images')->nullable();
            $table->text('requirements')->nullable(); // What to bring, skill level, etc.
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_events');
    }
};
