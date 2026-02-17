<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * Enquiries/Contact submissions from potential clients to providers.
     * Critical for abuse prevention and rate limiting.
     * 
     * Indexes support:
     * - Rate limiting by IP/email
     * - Provider notification queries
     * - Spam detection
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Optional: for authenticated users

            // Enquirer information
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message');
            
            // Abuse prevention tracking
            $table->string('ip_address', 45); // IPv6 support
            $table->string('user_agent')->nullable();
            $table->string('session_id')->nullable();
            
            // Status tracking
            $table->enum('status', ['pending', 'read', 'replied', 'spam', 'blocked'])->default('pending');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            
            // Spam detection flags
            $table->boolean('is_spam')->default(false);
            $table->text('spam_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes for rate limiting and abuse prevention
            // Critical: Check recent enquiries by IP for rate limiting
            $table->index(['ip_address', 'created_at'], 'idx_rate_limit_ip');
            
            // Check recent enquiries by email
            $table->index(['email', 'created_at'], 'idx_rate_limit_email');
            
            // Provider's enquiry inbox
            $table->index(['listing_id', 'status', 'created_at'], 'idx_provider_inbox');
            
            // Spam detection queries
            $table->index(['is_spam', 'created_at'], 'idx_spam_detection');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enquiries');
    }
}

