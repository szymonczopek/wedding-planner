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
            $table->dropColumn('invite_code');
        });

        Schema::table('weddings', function (Blueprint $table) {
            $table->string('invite_code')->unique();
        });

        Schema::table('wedding_user', function (Blueprint $table) {
            $table->boolean('selected')->default(false);
            $table->enum('role', array_column(UserRolesEnum::cases(), 'value'))->default(UserRolesEnum::WEDDING_PLANNER->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
