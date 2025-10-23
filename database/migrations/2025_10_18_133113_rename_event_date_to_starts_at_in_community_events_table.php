<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('community_events', 'event_date')) {
            Schema::table('community_events', function (Blueprint $table) {
                $table->renameColumn('event_date', 'starts_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('community_events', 'starts_at')) {
            Schema::table('community_events', function (Blueprint $table) {
                $table->renameColumn('starts_at', 'event_date');
            });
        }
    }
};

