<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConditionToMarketplaceItemsTable extends Migration
{
    public function up()
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->string('condition')->nullable()->after('category');
        });
    }

    public function down()
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->dropColumn('condition');
        });
    }
}
