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
        Schema::create('existing_attribute_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('existing_id');
            $table->uuid('attribute_id');
            $table->text('value');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('existing_id')->references('id')->on('existing')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['existing_id', 'attribute_id']);
            $table->index('existing_id');
            $table->index('attribute_id');
            $table->index('value');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('existing_attribute_values');
    }
};
