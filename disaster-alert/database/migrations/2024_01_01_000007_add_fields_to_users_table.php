<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    // run the migrations

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->enum('role', ['admin', 'citizen', 'relief_worker'])->default('citizen');

            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login')->nullable();
        });
    }

    // reverse the migrations

    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'phone', 'address', 'city', 'state', 
                'postal_code', 'latitude', 'longitude', 
                'is_active', 'last_login'
            ]);
        });
    }
};
