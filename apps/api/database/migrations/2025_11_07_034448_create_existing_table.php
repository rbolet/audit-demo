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
        Schema::create('existing', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('type_id');
            $table->uuid('location_id');
            $table->string('name', 255)->nullable();
            $table->string('label', 255);
            $table->string('label_abbr', 100)->nullable();
            $table->string('attribute_values_hash', 255)->nullable();
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_id')->references('id')->on('types')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade')->onUpdate('cascade');

            $table->index('type_id');
            $table->index('location_id');
            $table->index('attribute_values_hash');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('existing');
    }
};
