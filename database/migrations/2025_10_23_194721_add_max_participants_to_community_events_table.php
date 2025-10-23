<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('community_events', function (Blueprint $table) {
            $table->integer('max_participants')->nullable()->after('ends_at');
        });
    }

    public function down(): void
    {
        Schema::table('community_events', function (Blueprint $table) {
            $table->dropColumn('max_participants');
        });
    }
};

