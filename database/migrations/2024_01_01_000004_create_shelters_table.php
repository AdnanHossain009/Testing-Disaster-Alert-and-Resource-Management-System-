<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    // run the migrations


    public function up(): void
    {
        Schema::create('shelters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('capacity');
            $table->integer('current_occupancy')->default(0);

            $table->json('facilities');            // storing as JSON array
            $table->string('contact_phone');
            $table->string('contact_email')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Full', 'Maintenance'])->default('Active');
            $table->text('special_notes')->nullable();
            $table->timestamps();
            


            // indexes for performance


            $table->index(['status']);
            $table->index(['city', 'state']);
            $table->index(['latitude', 'longitude']);
            $table->index(['capacity', 'current_occupancy']);
        });
    }

    // reverse the migrations

    public function down(): void
    {
        Schema::dropIfExists('shelters');
    }
};
