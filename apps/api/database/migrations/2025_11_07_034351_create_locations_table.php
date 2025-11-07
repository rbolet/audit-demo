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
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('assessment_id');
            $table->uuid('parent_location_id')->nullable();
            $table->string('name', 255);
            $table->string('label_abbreviation', 50)->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('parent_location_id')->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');

            $table->index('assessment_id');
            $table->index('parent_location_id');
            $table->index(['parent_location_id', 'sort_order']);
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
