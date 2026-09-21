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
        Schema::create('calendar_event', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('calendar_id')->constrained('calendars')->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamp('date');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
    }
};
