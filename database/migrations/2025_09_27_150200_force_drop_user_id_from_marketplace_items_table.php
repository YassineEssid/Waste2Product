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
        // Supprimer la clé étrangère si la table existe et si la colonne existe
        if (Schema::hasTable('marketplace_items') && Schema::hasColumn('marketplace_items', 'user_id')) {
            Schema::table('marketplace_items', function (Blueprint $table) {
                // Pour SQLite, Laravel gère automatiquement la suppression des foreign keys
                // Pour MySQL, on essaie de supprimer la foreign key si elle existe
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Ignorer l'erreur si la foreign key n'existe pas
                }
                
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
