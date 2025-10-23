<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up(): void
{
    Schema::table('community_events', function (Blueprint $table) {
        if (!Schema::hasColumn('community_events', 'location')) {
            $table->string('location')->nullable();
        }
        if (!Schema::hasColumn('community_events', 'max_participants')) {
            $table->integer('max_participants')->nullable();
        }
    });
}



   public function down(): void
{
    Schema::table('community_events', function (Blueprint $table) {
        if (Schema::hasColumn('community_events', 'max_participants')) {
            $table->dropColumn('max_participants');
        }
        if (Schema::hasColumn('community_events', 'location')) {
            $table->dropColumn('location');
        }
    });
}

};
