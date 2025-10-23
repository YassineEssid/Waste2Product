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
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points'); // Can be positive or negative
            $table->string('type', 100); // 'event_attended', 'comment_posted', 'event_created', etc.
            $table->text('description');
            $table->string('reference_type', 100)->nullable(); // Model type (Event, Comment, etc.)
            $table->unsignedBigInteger('reference_id')->nullable(); // Model ID
            $table->integer('balance_after')->default(0); // Balance after transaction
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
