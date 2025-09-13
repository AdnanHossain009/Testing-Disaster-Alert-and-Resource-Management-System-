<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    // run the migrations


    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');             // requester name
            $table->string('phone');
            $table->string('email')->nullable();

            $table->enum('request_type', ['Shelter', 'Medical', 'Food', 'Water', 'Rescue', 'Other']);
            $table->text('description');
            $table->string('location');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('people_count')->default(1);

            $table->enum('urgency', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->enum('status', ['Pending', 'Assigned', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->text('special_needs')->nullable();          // medical, elderly, children, etc
            $table->timestamp('assigned_at')->nullable();
            
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            // indexes for performance

            $table->index(['status', 'urgency']);
            $table->index(['request_type']);
            $table->index(['location']);
            $table->index(['created_at']);
        });
    }

  // reverse the migrations

    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
