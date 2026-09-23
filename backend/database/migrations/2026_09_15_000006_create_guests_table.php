<?php

use App\Enums\AcquaintanceTypesEnum;
use App\Enums\DietsEnum;
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
        Schema::create('guests', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('wedding_id')->constrained('weddings')->cascadeOnDelete();
            $table->foreignUuid('table_id')->nullable()->constrained('tables')->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('address_id')->nullable()->constrained('addresses')->cascadeOnDelete();

            $table->uuid('main_guest_id')->nullable();

            $table->string('name');
            $table->string('surname');
            $table->enum('acquaintance_type', array_column(AcquaintanceTypesEnum::cases(), 'value'));
            $table->boolean('child');
            $table->boolean('groom_guest');
            $table->boolean('invitation_prepared');
            $table->boolean('invitation_delivered');
            $table->boolean('confirmed_attendance');
            $table->enum('diet', array_column(DietsEnum::cases(), 'value'));
            $table->float('table_position_x')->nullable();
            $table->float('table_position_y')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->foreign('main_guest_id')
                ->references('id')
                ->on('guests')
                ->nullOnDelete();
        });
    }

    public function down(): void {}
};
