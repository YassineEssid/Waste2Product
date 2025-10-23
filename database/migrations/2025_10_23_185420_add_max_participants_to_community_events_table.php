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
        Schema::table('community_events', function (Blueprint $table) {
            if (!Schema::hasColumn('community_events', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('community_events', 'location_lat')) {
                $table->decimal('location_lat', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('community_events', 'location_lng')) {
                $table->decimal('location_lng', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('community_events', 'max_participants')) {
                $table->integer('max_participants')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_events', function (Blueprint $table) {
            $table->dropColumn(['max_participants', 'location', 'location_lat', 'location_lng']);
        });
    }
};
