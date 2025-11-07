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
        Schema::create('valueset_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('valueset_id');
            $table->string('value', 255);
            $table->string('display_label', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('valueset_id')->references('id')->on('valuesets')->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['valueset_id', 'value']);
            $table->index('valueset_id');
            $table->index(['valueset_id', 'sort_order']);
            $table->index('is_active');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valueset_values');
    }
};
