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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->text('description');
            $table->string('icon', 100)->nullable(); // Font Awesome icon class
            $table->string('image')->nullable(); // Image path
            $table->string('color', 50)->default('#007bff'); // Badge color
            $table->enum('type', ['event', 'comment', 'participation', 'special', 'achievement'])->default('achievement');
            $table->integer('required_points')->default(0);
            $table->integer('required_count')->default(1); // Ex: 5 events, 10 comments
            $table->string('requirement_type', 100)->nullable(); // Ex: 'events_attended', 'comments_posted'
            $table->boolean('is_active')->default(true);
            $table->integer('rarity')->default(1); // 1=Common, 2=Rare, 3=Epic, 4=Legendary
            $table->timestamps();

            $table->index(['type', 'is_active']);
            $table->index('rarity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
