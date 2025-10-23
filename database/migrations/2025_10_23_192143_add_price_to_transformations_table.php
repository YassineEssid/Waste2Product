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
        Schema::table('transformations', function (Blueprint $table) {
            if (!Schema::hasColumn('transformations', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transformations', function (Blueprint $table) {
            if (Schema::hasColumn('transformations', 'price')) {
                $table->dropColumn('price');
            }
        });
    }
};
