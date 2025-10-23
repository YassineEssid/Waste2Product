<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_events', function (Blueprint $table) {
            if (!Schema::hasColumn('community_events', 'image')) {
                $table->string('image')->nullable()->after('description');
            }

            if (!Schema::hasColumn('community_events', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('community_events', function (Blueprint $table) {
            if (Schema::hasColumn('community_events', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('community_events', 'user_id')) {
                $table->dropForeign(['user_id']); // drop foreign key first
                $table->dropColumn('user_id');
            }
        });
    }
};
