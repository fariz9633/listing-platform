<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModerationLogsTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * Audit trail for all listing status changes and moderation actions.
     * Critical for:
     * - Compliance and accountability
     * - Detecting repeat offenders
     * - Admin performance tracking
     * - Dispute resolution
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Admin who performed action
            
            // Action details
            $table->string('action'); // 'approved', 'rejected', 'suspended', 'status_changed'
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->text('reason')->nullable(); // For rejections/suspensions
            
            // Context
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable(); // Additional context (e.g., automated flags)
            
            $table->timestamps();
            
            // Indexes for audit queries
            $table->index(['listing_id', 'created_at'], 'idx_listing_history');
            $table->index(['user_id', 'created_at'], 'idx_admin_actions');
            $table->index(['action', 'created_at'], 'idx_action_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moderation_logs');
    }
}

