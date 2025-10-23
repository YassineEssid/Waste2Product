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
        Schema::table('repair_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('repair_requests', 'urgency')) {
                $table->enum('urgency', ['low', 'medium', 'high'])->default('medium')->after('description');
            }
            if (!Schema::hasColumn('repair_requests', 'budget')) {
                $table->decimal('budget', 10, 2)->nullable()->after('urgency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_requests', function (Blueprint $table) {
            if (Schema::hasColumn('repair_requests', 'urgency')) {
                $table->dropColumn('urgency');
            }
            if (Schema::hasColumn('repair_requests', 'budget')) {
                $table->dropColumn('budget');
            }
        });
    }
};
