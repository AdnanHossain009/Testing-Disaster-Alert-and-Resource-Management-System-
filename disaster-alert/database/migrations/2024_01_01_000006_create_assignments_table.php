<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // run the migrations


    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {

            $table->id();
            $table->foreignId('request_id')->constrained()->onDelete('cascade');
            $table->foreignId('shelter_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('assigned_at');

            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();

            $table->enum('status', ['Assigned', 'Checked In', 'Checked Out', 'Cancelled'])->default('Assigned');
            $table->text('notes')->nullable();
            $table->timestamps();

            // indexes for performance
            $table->index(['request_id']);  
            $table->index(['status']);
            $table->index(['assigned_at']);
            $table->unique(['request_id']);    // one assignment per request
        });
    }

    // reverse the migrations

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
