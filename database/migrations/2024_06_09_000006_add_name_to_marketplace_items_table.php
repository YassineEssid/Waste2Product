<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToMarketplaceItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('marketplace_items', 'name')) {
            Schema::table('marketplace_items', function (Blueprint $table) {
                $table->string('name')->nullable()->after('title');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('marketplace_items', 'name')) {
            Schema::table('marketplace_items', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
    }
}
