<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la clé étrangère existe avant de la supprimer
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'marketplace_items'
              AND REFERENCED_TABLE_NAME = 'users'
              AND COLUMN_NAME = 'user_id'
              AND CONSTRAINT_SCHEMA = DATABASE()
        ");

        if (!empty($foreignKeys)) {
            Schema::table('marketplace_items', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        // Supprimer la colonne si elle existe
        if (Schema::hasColumn('marketplace_items', 'user_id')) {
            Schema::table('marketplace_items', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
