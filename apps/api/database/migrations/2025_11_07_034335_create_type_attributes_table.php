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
        Schema::create('type_attributes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('type_id');
            $table->uuid('attribute_id');
            $table->integer('label_concat_order')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade')->onUpdate('cascade');

            $table->unique(['type_id', 'attribute_id']);
            $table->index('type_id');
            $table->index('attribute_id');
            $table->index('label_concat_order');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_attributes');
    }
};
