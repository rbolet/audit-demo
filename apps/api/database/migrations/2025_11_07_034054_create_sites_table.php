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
        Schema::create('sites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('site_name', 255);
            $table->string('site_address', 255);
            $table->string('site_address_2', 255)->nullable();
            $table->string('site_city', 100);
            $table->string('site_state', 50);
            $table->string('site_postal_code', 20);
            $table->string('site_contact_name', 255)->nullable();
            $table->string('site_contact_phone', 50)->nullable();
            $table->string('site_contact_email', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('site_name');
            $table->index('site_postal_code');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
