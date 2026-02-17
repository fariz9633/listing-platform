<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRateLimitsTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * Dedicated table for rate limiting tracking.
     * Supports multiple rate limit types (enquiries, listings, etc.)
     * Can be cleaned up periodically (data older than 24 hours)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rate_limits', function (Blueprint $table) {
            $table->id();
            $table->string('key'); // e.g., 'enquiry:ip:192.168.1.1', 'enquiry:email:test@example.com'
            $table->string('type'); // 'enquiry', 'listing_submission', etc.
            $table->unsignedInteger('hits')->default(1);
            $table->timestamp('expires_at');
            $table->timestamps();
            
            // Composite index for fast lookups
            $table->index(['key', 'type', 'expires_at'], 'idx_rate_limit_check');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rate_limits');
    }
}

