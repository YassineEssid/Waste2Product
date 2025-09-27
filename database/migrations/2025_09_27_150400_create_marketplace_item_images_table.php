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
        Schema::create('marketplace_item_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('marketplace_item_id');
            $table->string('path');
            $table->timestamps();
            $table->foreign('marketplace_item_id')->references('id')->on('marketplace_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_item_images');
    }
};
