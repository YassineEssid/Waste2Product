<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('community_events', 'ends_at')) {
            Schema::table('community_events', function (Blueprint $table) {
                $table->dateTime('ends_at')->nullable()->after('starts_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('community_events', 'ends_at')) {
            Schema::table('community_events', function (Blueprint $table) {
                $table->dropColumn('ends_at');
            });
        }
    }
};
