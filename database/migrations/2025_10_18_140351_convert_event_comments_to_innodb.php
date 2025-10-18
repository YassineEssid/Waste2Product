<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert event_comments table to InnoDB for foreign key constraints
        DB::statement('ALTER TABLE event_comments ENGINE = InnoDB');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to MyISAM if needed
        DB::statement('ALTER TABLE event_comments ENGINE = MyISAM');
    }
};
