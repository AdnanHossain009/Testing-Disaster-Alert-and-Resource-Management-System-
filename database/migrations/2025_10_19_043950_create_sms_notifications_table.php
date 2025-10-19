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
        Schema::create('sms_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('notification_type'); // alert_created, request_submitted, shelter_assigned, status_updated
            $table->text('message_content');
            $table->string('urgency_level')->nullable(); // Critical, High, Medium, Low
            $table->unsignedBigInteger('reference_id')->nullable(); // alert_id, request_id, etc.
            $table->string('reference_type')->nullable(); // Alert, HelpRequest, Assignment
            $table->enum('status', ['pending', 'logged', 'failed'])->default('logged');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('phone_number');
            $table->index('notification_type');
            $table->index('urgency_level');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_notifications');
    }
};
