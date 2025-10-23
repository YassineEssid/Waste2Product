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
        Schema::table('messages', function (Blueprint $table) {
            // Rename user_id to sender_id
            $table->renameColumn('user_id', 'sender_id');

            // Rename body to message
            $table->renameColumn('body', 'message');

            // Drop read_at and add is_read
            $table->dropColumn('read_at');
            $table->boolean('is_read')->default(false)->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Revert the changes
            $table->renameColumn('sender_id', 'user_id');
            $table->renameColumn('message', 'body');
            $table->dropColumn('is_read');
            $table->timestamp('read_at')->nullable()->after('body');
        });
    }
};
