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
        Schema::create('tables', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('wedding_id')->constrained('weddings')->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('name');
            $table->integer('capacity')->nullable();
            $table->float('position_x')->nullable();
            $table->float('position_y')->nullable();
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->float('radius')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {}
};
