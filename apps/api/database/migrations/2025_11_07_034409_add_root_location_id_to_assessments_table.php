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
        Schema::table('assessments', function (Blueprint $table) {
            $table->uuid('root_location_id')->nullable()->after('site_id');
            $table->foreign('root_location_id')->references('id')->on('locations')->onDelete('set null')->onUpdate('cascade');
            $table->index('root_location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['root_location_id']);
            $table->dropIndex(['root_location_id']);
            $table->dropColumn('root_location_id');
        });
    }
};
