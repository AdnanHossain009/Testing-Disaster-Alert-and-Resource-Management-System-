<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
     // run the migrations
     
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->enum('severity', ['Low', 'Moderate', 'High', 'Critical']);
            $table->enum('type', ['Flood', 'Earthquake', 'Cyclone', 'Fire', 'Health Emergency', 'Other']);
            $table->string('location');

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['Active', 'Resolved', 'Monitoring'])->default('Active');
            $table->timestamp('issued_at');

            $table->timestamp('expires_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // indexes for performance

            $table->index(['status', 'severity']);
            $table->index(['location']);
            $table->index(['issued_at', 'expires_at']);
        });
    }

    
     // reverse the migrations
     
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
