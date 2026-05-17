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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('viewer')->change();
        });

        // Update existing users with no role to 'viewer'
        \DB::table('users')->whereNull('role')->update(['role' => 'viewer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert the default change as it's additive
    }
};
