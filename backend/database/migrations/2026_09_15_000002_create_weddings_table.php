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
        Schema::create('weddings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('name');
            $table->timestamp('date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {}
};
