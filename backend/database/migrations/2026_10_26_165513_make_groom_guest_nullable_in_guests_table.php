<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->boolean('groom_guest')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('guests')->whereNull('groom_guest')->update(['groom_guest' => false]);

        Schema::table('guests', function (Blueprint $table) {
            $table->boolean('groom_guest')->nullable(false)->change();
        });
    }
};
