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
        Schema::table('transformations', function (Blueprint $table) {
            // Renommer les colonnes existantes
            $table->renameColumn('new_product_name', 'product_title');

            // Ajouter les nouvelles colonnes après description
            $table->json('impact')->nullable()->after('description'); // {co2_saved, waste_reduced}
            $table->decimal('price', 10, 2)->nullable()->after('impact');
            $table->json('before_images')->nullable()->after('before_image');
            $table->json('after_images')->nullable()->after('after_image');
            $table->json('process_images')->nullable()->after('after_images');
            $table->integer('time_spent_hours')->nullable()->after('process_images');
            $table->decimal('materials_cost', 10, 2)->default(0)->after('time_spent_hours');
            $table->boolean('is_featured')->default(false)->after('status');
            $table->integer('views_count')->default(0)->after('is_featured');
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');

            // Modifier le enum status pour ajouter 'planned' et 'published'
            DB::statement("ALTER TABLE transformations MODIFY COLUMN status ENUM('planned', 'pending', 'in_progress', 'completed', 'published') DEFAULT 'planned'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transformations', function (Blueprint $table) {
            $table->renameColumn('product_title', 'new_product_name');
            $table->dropColumn([
                'impact',
                'price',
                'before_images',
                'after_images',
                'process_images',
                'time_spent_hours',
                'materials_cost',
                'is_featured',
                'views_count',
                'user_id'
            ]);
        });
    }
};
