<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListingsTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * Core aggregate for the listing platform.
     * Indexes are designed for:
     * - Public search queries (status, category, location, price)
     * - Provider dashboard queries (user_id, status)
     * - Admin moderation queries (status, created_at)
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Core listing data
            $table->string('title');
            $table->text('description');
            $table->string('slug')->unique();
            
            // Categorization
            $table->string('category'); // e.g., 'escort', 'massage', etc.
            $table->string('city');
            $table->string('suburb');
            
            // Pricing
            $table->enum('pricing_type', ['hourly', 'fixed'])->default('hourly');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('price_min', 10, 2)->nullable(); // For hourly ranges
            $table->decimal('price_max', 10, 2)->nullable();
            
            // Status lifecycle - enforced at domain level
            // Valid transitions must be handled in service layer
            $table->enum('status', [
                'draft',
                'pending_moderation',
                'approved',
                'rejected',
                'suspended'
            ])->default('draft');
            
            // Moderation tracking
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('moderated_at')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('moderation_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // SEO & Performance
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('enquiry_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Critical indexes for search performance
            // Composite index for public search: status + category + city
            $table->index(['status', 'category', 'city'], 'idx_public_search');
            
            // Index for location-based search
            $table->index(['city', 'suburb'], 'idx_location');
            
            // Index for price range queries
            $table->index(['pricing_type', 'price'], 'idx_pricing');
            
            // Index for provider dashboard
            $table->index(['user_id', 'status'], 'idx_provider_listings');
            
            // Index for admin moderation queue
            $table->index(['status', 'created_at'], 'idx_moderation_queue');
            
            // Full-text search index for title and description
            // Note: For production at scale (1M+ listings), consider:
            // - Elasticsearch/Algolia for advanced search
            // - Separate search index table
            // - Redis for caching popular searches
            $table->index('title');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('listings');
    }
}

