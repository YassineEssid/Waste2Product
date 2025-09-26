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
        Schema::create('transformations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // artisan
            $table->foreignId('waste_item_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('process_description');
            $table->json('before_images')->nullable();
            $table->json('after_images')->nullable();
            $table->json('process_images')->nullable();
            $table->integer('time_spent_hours')->nullable(); // Time in hours
            $table->decimal('materials_cost', 10, 2)->default(0);
            $table->decimal('co2_saved', 8, 2)->default(0); // kg of CO2
            $table->decimal('waste_reduced', 8, 2)->default(0); // kg of waste
            $table->enum('status', ['planned', 'in_progress', 'completed', 'published'])->default('planned');
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transformations');
    }
};
