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
        Schema::create('in_app_notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('recipient_type', ['admin', 'citizen'])->default('admin'); // Who sees it
            $table->unsignedBigInteger('user_id')->nullable(); // For citizen notifications
            $table->enum('type', ['alert_created', 'request_submitted', 'shelter_assigned', 'status_updated']);
            $table->string('title'); // e.g., "🚨 HIGH ALERT: Flood Warning"
            $table->text('message'); // Full notification message
            $table->string('icon')->default('🔔'); // Emoji icon
            $table->string('color')->default('#3498db'); // UI color
            $table->unsignedBigInteger('reference_id')->nullable(); // alert_id, request_id, etc.
            $table->string('reference_type')->nullable(); // Alert, HelpRequest, Assignment
            $table->boolean('seen')->default(false); // Mark as read
            $table->timestamp('seen_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('recipient_type');
            $table->index('user_id');
            $table->index('type');
            $table->index('seen');
            $table->index('created_at');
            
            // Foreign key for user
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_app_notifications');
    }
};
