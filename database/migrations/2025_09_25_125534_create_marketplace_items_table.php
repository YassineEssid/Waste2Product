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
        Schema::create('marketplace_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // seller/artisan
            $table->foreignId('transformation_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('category');
            $table->enum('condition', ['new', 'like_new', 'good', 'fair', 'poor'])->default('new');
            $table->json('images')->nullable();
            $table->integer('quantity')->default(1);
            $table->boolean('is_negotiable')->default(true);
            $table->enum('delivery_method', ['pickup', 'delivery', 'both'])->default('pickup');
            $table->text('delivery_notes')->nullable();
            $table->enum('status', ['available', 'sold', 'reserved', 'removed'])->default('available');
            $table->boolean('is_featured')->default(false);
            $table->integer('views_count')->default(0);
            $table->timestamp('promoted_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_items');
    }
};
