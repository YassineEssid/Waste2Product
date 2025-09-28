<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryToMarketplaceItemsTable extends Migration
{
    public function up()
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('marketplace_items', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
