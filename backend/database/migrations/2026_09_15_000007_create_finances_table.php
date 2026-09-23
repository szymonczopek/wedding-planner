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
        Schema::create('finances', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('wedding_id')->constrained('weddings')->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('name');
            $table->double('amount');
            $table->string('category')->nullable();
            $table->string('contact')->nullable();
            $table->timestamp('payment_deadline')->nullable();
            $table->string('payment_status')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {}
};
