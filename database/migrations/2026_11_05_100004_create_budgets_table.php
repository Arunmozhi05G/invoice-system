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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');

            $table->string('period_type', 20);

            $table->unsignedSmallInteger('year')->index();

            $table->unsignedTinyInteger('month')->nullable();

            $table->decimal('amount', 12, 2);

            $table->timestamps();

            $table->unique([
                'category',
                'period_type',
                'year',
                'month',
            ], 'budgets_category_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
