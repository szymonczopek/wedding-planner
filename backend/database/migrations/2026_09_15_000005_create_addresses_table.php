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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('street');
            $table->string('house_number');
            $table->string('local_number')->nullable();
            $table->string('city');
            $table->string('postal_code');
            $table->string('country');
        });
    }

    public function down(): void
    {
    }
};
