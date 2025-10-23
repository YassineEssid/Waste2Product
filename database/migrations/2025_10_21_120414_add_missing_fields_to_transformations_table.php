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
            // Rename column if exists
            if (Schema::hasColumn('transformations', 'new_product_name')) {
                $table->renameColumn('new_product_name', 'product_title');
            }

            // Add new columns safely
            if (!Schema::hasColumn('transformations', 'impact')) {
                $table->json('impact')->nullable()->after('description'); // {co2_saved, waste_reduced}
            }
            if (!Schema::hasColumn('transformations', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('impact');
            }
            if (!Schema::hasColumn('transformations', 'before_images')) {
                $table->json('before_images')->nullable()->after('before_image');
            }
            if (!Schema::hasColumn('transformations', 'after_images')) {
                $table->json('after_images')->nullable()->after('after_image');
            }
            if (!Schema::hasColumn('transformations', 'process_images')) {
                $table->json('process_images')->nullable()->after('after_images');
            }
            if (!Schema::hasColumn('transformations', 'time_spent_hours')) {
                $table->integer('time_spent_hours')->nullable()->after('process_images');
            }
            if (!Schema::hasColumn('transformations', 'materials_cost')) {
                $table->decimal('materials_cost', 10, 2)->default(0)->after('time_spent_hours');
            }
            if (!Schema::hasColumn('transformations', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            if (!Schema::hasColumn('transformations', 'views_count')) {
                $table->integer('views_count')->default(0)->after('is_featured');
            }
            if (!Schema::hasColumn('transformations', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            }
        });

        // Modify enum safely
        $statusColumnExists = Schema::hasColumn('transformations', 'status');
        if ($statusColumnExists) {
            DB::statement("ALTER TABLE transformations MODIFY COLUMN status ENUM('planned', 'pending', 'in_progress', 'completed', 'published') DEFAULT 'planned'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transformations', function (Blueprint $table) {
            if (Schema::hasColumn('transformations', 'product_title')) {
                $table->renameColumn('product_title', 'new_product_name');
            }

            $dropColumns = [
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
            ];

            foreach ($dropColumns as $col) {
                if (Schema::hasColumn('transformations', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
