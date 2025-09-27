<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUserIdFromMarketplaceItems extends Migration
{
    public function up()
    {
        // For SQLite, you must recreate the table without user_id
        if (Schema::hasColumn('marketplace_items', 'user_id')) {
            Schema::rename('marketplace_items', 'marketplace_items_old');

            Schema::create('marketplace_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('seller_id');
                $table->unsignedBigInteger('transformation_id')->nullable();
                $table->string('title');
                $table->text('description');
                $table->decimal('price', 10, 2);
                $table->string('category');
                $table->string('condition');
                $table->json('images')->nullable();
                $table->integer('quantity')->default(1);
                $table->boolean('is_negotiable')->default(false);
                $table->string('delivery_method')->nullable();
                $table->text('delivery_notes')->nullable();
                $table->string('status')->default('available');
                $table->boolean('is_featured')->default(false);
                $table->integer('views_count')->default(0);
                $table->timestamp('promoted_until')->nullable();
                $table->timestamps();
            });

            DB::statement('INSERT INTO marketplace_items (id, seller_id, transformation_id, title, description, price, category, condition, images, quantity, is_negotiable, delivery_method, delivery_notes, status, is_featured, views_count, promoted_until, created_at, updated_at)
                SELECT id, seller_id, transformation_id, title, description, price, category, condition, images, quantity, is_negotiable, delivery_method, delivery_notes, status, is_featured, views_count, promoted_until, created_at, updated_at
                FROM marketplace_items_old');

            Schema::drop('marketplace_items_old');
        }
    }

    public function down()
    {
        // ...optional: recreate user_id column if needed...
    }
}
