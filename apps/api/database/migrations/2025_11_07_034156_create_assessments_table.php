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
        Schema::create('assessments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('site_id');
            $table->date('scheduled_date');
            $table->uuid('assigned_to_id')->nullable();
            $table->timestamp('assigned_date')->nullable();
            $table->enum('status', ['PLANNED', 'ASSIGNED', 'IN_PROGRESS', 'IN_QC', 'COMPLETE'])->default('PLANNED');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('assigned_to_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');

            $table->index('site_id');
            $table->index('assigned_to_id');
            $table->index('scheduled_date');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
