<?php

use App\Enums\UserRolesEnum;
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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', array_column(UserRolesEnum::cases(), 'value'))->default(UserRolesEnum::WEDDING_PLANNER->value);
            $table->string('user_invite_code')->unique()->default('xxx');
        });
    }

    public function down(): void
    {
    }
};
