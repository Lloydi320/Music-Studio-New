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
        Schema::create('reschedule_requests', function (Blueprint $table) {
            $table->id();
            
            // Request identification
            $table->string('reference')->unique(); // Unique reference for the reschedule request
            
            // Resource information (what's being rescheduled)
            $table->string('resource_type'); // 'Booking' or 'InstrumentRental'
            $table->unsignedBigInteger('resource_id'); // ID of the booking or rental
            
            // User information
            $table->unsignedBigInteger('user_id');
            $table->string('customer_name');
            $table->string('customer_email');
            
            // Original booking/rental details
            $table->json('original_data'); // Store original booking/rental data
            
            // Requested changes
            $table->json('requested_data'); // Store requested changes
            
            // For studio bookings
            $table->date('original_date')->nullable();
            $table->string('original_time_slot')->nullable();
            $table->integer('original_duration')->nullable();
            $table->date('requested_date')->nullable();
            $table->string('requested_time_slot')->nullable();
            $table->integer('requested_duration')->nullable();
            
            // For instrument rentals
            $table->date('original_start_date')->nullable();
            $table->date('original_end_date')->nullable();
            $table->date('requested_start_date')->nullable();
            $table->date('requested_end_date')->nullable();
            
            // Request details
            $table->text('reason')->nullable(); // Customer's reason for rescheduling
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Conflict detection
            $table->boolean('has_conflict')->default(false);
            $table->json('conflict_details')->nullable(); // Details about conflicts
            
            // Admin handling
            $table->unsignedBigInteger('handled_by')->nullable(); // Admin user ID who handled the request
            $table->timestamp('handled_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Notification tracking
            $table->boolean('customer_notified')->default(false);
            $table->boolean('admin_notified')->default(true);
            $table->timestamp('customer_notified_at')->nullable();
            $table->timestamp('admin_notified_at')->nullable();
            
            // Metadata
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Additional metadata
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['resource_type', 'resource_id']);
            $table->index('user_id');
            $table->index('status');
            $table->index('priority');
            $table->index('has_conflict');
            $table->index('handled_by');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
            $table->index(['resource_type', 'status']);
            
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('handled_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reschedule_requests');
    }
};