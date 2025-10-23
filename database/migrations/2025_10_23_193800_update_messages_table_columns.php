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
        // Skip if table doesn't exist yet
        if (!Schema::hasTable('messages')) {
            return;
        }

        Schema::table('messages', function (Blueprint $table) {
            // Rename user_id to sender_id only if column exists
            if (Schema::hasColumn('messages', 'user_id')) {
                $table->renameColumn('user_id', 'sender_id');
            }

            // Rename body to message only if column exists
            if (Schema::hasColumn('messages', 'body')) {
                $table->renameColumn('body', 'message');
            }

            // Drop read_at and add is_read
            if (Schema::hasColumn('messages', 'read_at')) {
                $table->dropColumn('read_at');
            }
            
            if (!Schema::hasColumn('messages', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('message');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip if table doesn't exist
        if (!Schema::hasTable('messages')) {
            return;
        }

        Schema::table('messages', function (Blueprint $table) {
            // Revert the changes only if columns exist
            if (Schema::hasColumn('messages', 'sender_id')) {
                $table->renameColumn('sender_id', 'user_id');
            }
            
            if (Schema::hasColumn('messages', 'message')) {
                $table->renameColumn('message', 'body');
            }
            
            if (Schema::hasColumn('messages', 'is_read')) {
                $table->dropColumn('is_read');
            }
            
            if (!Schema::hasColumn('messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('body');
            }
        });
    }
};
