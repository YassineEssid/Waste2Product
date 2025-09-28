<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityToMarketplaceItemsTable extends Migration
{
    public function up()
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->after('status');
        });
    }

    public function down()
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
}
