# listing-platform

## Overview

This is a production-quality listing platform built with Laravel 8, designed for adult service providers (similar to realbabes.com.au). The platform handles listing management, moderation workflows, enquiry submissions, and abuse prevention.

**Tech Stack:**
- Laravel 8 
- Blade templates 
- Bootstrap 5 
- MySQL database

## What Was Built (Core System)

### ✅ Implemented Features

1. **Database Architecture**
   - Users table with role-based access (guest, provider, admin)
   - Listings table with full lifecycle management
   - Enquiries table with abuse tracking
   - Moderation logs for audit trail
   - Categories and rate limiting tables
   - Comprehensive indexes for performance

2. **Domain Models**
   - Clean Eloquent models with business logic
   - Explicit status transition validation
   - Proper relationships and scopes
   - No anemic models - domain logic lives in models

3. **Authorization Layer**
   - Laravel Policies for all listing operations
   - Role-based access control
   - Granular permissions (view, create, update, delete, moderate)

4. **Service Layer**
   - `ListingService` - handles all listing business logic
   - `EnquiryService` - manages enquiries with rate limiting and spam detection
   - `ListingSearchService` - centralized search and filtering logic
   - Controllers are THIN - only routing and orchestration

5. **Public Features**
   - SEO-first listing search with filters (keyword, category, location, price)
   - Stable, crawlable URLs
   - Server-rendered listing detail pages
   - Enquiry/contact form with double-submit protection

6. **Provider Dashboard**
   - View all listings with status indicators
   - Create and edit listings
   - Submit listings for moderation
   - See rejection reasons and moderation notes
   - Delete draft listings

7. **Admin Moderation**
   - Moderation queue (pending listings)
   - Approve/reject/suspend actions
   - Moderation notes and reasons
   - Full audit trail via moderation logs

8. **Abuse Prevention**
   - Rate limiting (IP and email-based)
   - Basic spam detection
   - Session-based double-submit protection
   - IP address and user agent tracking

### ❌ Intentionally Not Built

- User authentication scaffolding (would use Laravel Breeze in production)
- Media uploads (placeholder structure exists)
- Email notifications (service layer ready for integration)
- Background jobs (structure supports it)
- Advanced search (Elasticsearch integration)
- Payment/promotion features
- API endpoints
- Comprehensive test suite (focused on architecture)









## System Design & Scaling Strategy

### 1. Moderation at Scale

**Current Implementation:**
- Manual review queue for admins
- Status-based workflow with audit logs
- Rejection reasons visible to providers

**Scaling to 10k+ listings/day:**

a) **Automated Pre-Screening**
   - ML-based content classifier (train on approved/rejected history)
   - Auto-approve low-risk listings (established providers, clean history)
   - Auto-flag high-risk content (banned keywords, suspicious patterns)
   - Reduce manual review to ~20% of submissions

b) **Provider Trust Score**
   - Track approval rate, rejection rate, suspension history
   - Fast-track trusted providers (>95% approval rate, 50+ approved listings)
   - Increase scrutiny for new/problematic providers
   - Implement probation period for repeat offenders

c) **Detecting Low-Quality/Fake Listings**
   - Duplicate detection (fuzzy matching on title/description)
   - Image reverse search (detect stock photos)
   - Phone/email verification (prevent throwaway contacts)
   - Minimum content quality thresholds (description length, detail level)
   - Pattern detection (same IP creating multiple listings)

d) **Handling Repeat Offenders**
   - Escalating penalties: warning → probation → suspension → ban
   - IP/email blacklisting for severe violations
   - Require manual approval for all listings from flagged accounts
   - Implement appeal process with human review

**Implementation Priority:**
1. Provider trust score (2-3 days) - immediate impact
2. Duplicate detection (1-2 days) - prevents spam
3. ML classifier (2-3 weeks) - requires training data
4. Image verification (1 week) - high-value feature

### 2. Abuse & Fraud Prevention

**Current Implementation:**
- IP-based rate limiting (3 enquiries/hour)
- Email-based rate limiting (5 enquiries/day)
- Basic spam keyword detection
- Session-based double-submit protection

**Advanced Strategies:**

a) **Spam Submissions**
   - Honeypot fields (hidden form fields)
   - CAPTCHA for suspicious IPs (after rate limit hit)
   - Behavioral analysis (form fill time, mouse movement)
   - Bayesian spam filter trained on marked spam
   - Disposable email detection (block temporary email services)

b) **Fake Providers**
   - Phone verification via SMS (one-time code)
   - Email verification (required before first listing)
   - Identity verification for high-volume providers (optional tier)
   - Social proof (link to existing online presence)
   - Deposit/fee for new accounts (refundable after first approval)

c) **Automated Scraping**
   - Rate limiting at CDN level (Cloudflare)
   - Require authentication for bulk access
   - Implement robots.txt and meta tags
   - Detect scraper patterns (rapid sequential requests)
   - Serve different content to suspected bots (honeypot listings)
   - API with authentication for legitimate integrations

**Implementation:**
```php
class EnquiryService {
    protected function checkAdvancedRateLimits($request) {
        // Existing IP/email limits
        $this->checkRateLimits($request);

        // Fingerprint-based limiting
        $fingerprint = $this->generateFingerprint($request);
        if (Cache::get("fingerprint:{$fingerprint}") > 10) {
            throw new TooManyRequestsException();
        }

        // Behavioral checks
        if ($this->isSuspiciousBehavior($request)) {
            // Require CAPTCHA
            return ['requires_captcha' => true];
        }
    }
}
```

### 3. Performance & Caching Strategy

**What to Cache:**

a) **Query-Level Caching**
   - Popular searches (category + city combinations)
   - Homepage featured listings
   - Category/city filter options
   - Provider dashboard listing counts
   - Cache TTL: 5-15 minutes

b) **Page-Level Caching**
   - Approved listing detail pages (cache until updated)
   - Search result pages (cache per filter combination)
   - Static pages (about, terms, privacy)
   - Cache TTL: 1-24 hours

c) **Object Caching**
   - User permissions/roles (session duration)
   - Category tree (1 hour)
   - Rate limit counters (sliding window)

**Where to Cache:**

- **Application Level**: Laravel Cache (Redis/Memcached)
- **Database Level**: MySQL query cache, prepared statements
- **HTTP Level**: Varnish/Cloudflare for anonymous users
- **CDN**: Static assets, images

**Cache Invalidation Strategy:**

```php
// Event-based invalidation
class ListingObserver {
    public function updated(Listing $listing) {
        // Clear specific listing cache
        Cache::forget("listing:{$listing->id}");
        Cache::forget("listing:slug:{$listing->slug}");

        // Clear related search caches
        Cache::tags(['search', "category:{$listing->category}"])->flush();

        // Clear provider dashboard
        Cache::forget("provider:{$listing->user_id}:listings");
    }
}
```

**Why This Approach:**
- Balances freshness vs performance
- Granular invalidation prevents stale data
- Tag-based cache clearing for related content
- Separate cache strategies for different user types (guest vs authenticated)

### 4. Search at Scale (10k → 1M listings)

**Current Implementation:**
- MySQL full-text search on title/description
- Composite indexes for common filter combinations
- Scopes for category, location, price filtering

**Evolution Path:**

**Phase 1: Optimized MySQL (10k-100k listings)**
- Current implementation sufficient
- Add covering indexes for common queries
- Implement query result caching
- Estimated performance: <100ms for most searches

**Phase 2: Read Replicas (100k-500k listings)**
- Separate read/write databases
- Route all searches to read replicas
- Master handles writes only
- Reduces load on primary database
- Estimated performance: <200ms

**Phase 3: Elasticsearch (500k+ listings)**
- Migrate search to Elasticsearch cluster
- Keep MySQL as source of truth
- Sync via Laravel Scout or custom indexer
- Benefits:
  - Sub-50ms search response times
  - Advanced features (fuzzy search, typo tolerance, relevance scoring)
  - Faceted search (dynamic filter counts)
  - Geo-spatial search (distance-based)
  - Autocomplete/suggestions

**Implementation Example:**

```php
// Phase 3: Elasticsearch integration
class ListingSearchService {
    public function search(array $filters) {
        // Use Elasticsearch for search
        $results = Listing::search($filters['keyword'] ?? '')
            ->where('status', 'approved')
            ->when($filters['category'] ?? null, fn($q, $cat) =>
                $q->where('category', $cat)
            )
            ->when($filters['city'] ?? null, fn($q, $city) =>
                $q->where('city', $city)
            )
            ->paginate(20);

        // Fallback to MySQL if ES unavailable
        if (!$this->elasticsearchAvailable()) {
            return $this->mysqlSearch($filters);
        }

        return $results;
    }
}
```

**Database Sharding (1M+ listings):**
- Shard by geographic region (city/state)
- Each shard handles local searches
- Cross-shard searches only for national queries
- Reduces index size and improves performance

### 5. Long-Term Evolution

**A. Paid Promotions & Featured Listings**

**Architecture Changes:**
- New table: `listing_promotions` (listing_id, type, starts_at, expires_at, price)
- New table: `transactions` (user_id, amount, type, status, payment_gateway_id)
- Payment gateway integration (Stripe/PayPal)

**Implementation:**
```php
// Promotion model
class ListingPromotion extends Model {
    public function isActive() {
        return $this->starts_at <= now() && $this->expires_at >= now();
    }
}

// Modified search to prioritize promoted listings
class ListingSearchService {
    public function search(array $filters) {
        return Listing::approved()
            ->with('activePromotion')
            ->orderByRaw('CASE WHEN listing_promotions.id IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('created_at', 'desc')
            ->applyFilters($filters)
            ->paginate(20);
    }
}
```

**Revenue Model:**
- Featured placement: $50/week
- Category top spot: $100/week
- Homepage carousel: $200/week
- Boost (temporary top ranking): $10/day

**B. Regional Expansion**

**Multi-Region Strategy:**
- Subdomain per region: sydney.platform.com, melbourne.platform.com
- Shared codebase, region-specific configuration
- Regional databases with central user database
- CDN with regional edge caching

**Database Architecture:**
```
Central DB (users, transactions, global config)
    ↓
Regional DBs (listings, enquiries, moderation_logs)
    - AU Database (Sydney, Melbourne, Brisbane)
    - US Database (LA, NYC, Miami)
    - UK Database (London, Manchester)
```

**Implementation:**
```php
// config/database.php
'connections' => [
    'central' => [...],
    'region_au' => [...],
    'region_us' => [...],
    'region_uk' => [...],
],

// Dynamic connection based on region
class Listing extends Model {
    public function getConnectionName() {
        return 'region_' . config('app.region');
    }
}
```

**C. Mobile Apps (iOS/Android)**

**API-First Approach:**
- Build RESTful API alongside existing web routes
- Shared business logic (services remain unchanged)
- API versioning (/api/v1/listings)
- OAuth2 authentication (Laravel Passport)

**Architecture:**
```
Mobile Apps
    ↓
API Gateway (rate limiting, auth)
    ↓
Laravel API Controllers (thin)
    ↓
Existing Services (reused)
    ↓
Models & Database
```

**Key Endpoints:**
```php
// routes/api.php
Route::middleware('auth:api')->group(function() {
    Route::get('/listings', [Api\ListingController::class, 'index']);
    Route::get('/listings/{listing}', [Api\ListingController::class, 'show']);
    Route::post('/listings', [Api\ListingController::class, 'store']);
    Route::post('/enquiries', [Api\EnquiryController::class, 'store']);
});
```

**Benefits of Current Architecture:**
- Service layer already separates business logic
- Easy to add API controllers that call same services
- Policies work identically for web and API
- Minimal code duplication

**D. Advanced Features**

1. **Saved Searches & Alerts**
   - Table: `saved_searches` (user_id, filters_json, notify_frequency)
   - Background job checks for new matches
   - Email/push notifications

2. **Messaging System**
   - Direct messaging between users
   - Table: `conversations`, `messages`
   - Real-time via Laravel Echo + Pusher

3. **Reviews & Ratings**
   - Table: `reviews` (listing_id, user_id, rating, comment)
   - Verified reviews (only after enquiry)
   - Moderation for review content

4. **Analytics Dashboard**
   - Provider insights (views, enquiries, conversion rate)
   - Admin analytics (approval rates, popular categories)
   - Integration with Google Analytics

## Key Trade-Offs & Decisions

### 1. Monolith vs Microservices
**Decision:** Monolith
**Rationale:**
- Faster initial development
- Simpler deployment and debugging
- Sufficient for expected scale (1M listings)
- Can extract services later if needed (moderation service, search service)

### 2. Server-Rendered vs SPA
**Decision:** Server-rendered Blade templates
**Rationale:**
- SEO is critical for listing discovery
- Simpler architecture, less JavaScript complexity
- Better performance on low-end devices
- Progressive enhancement for interactivity

### 3. MySQL vs NoSQL
**Decision:** MySQL (relational)
**Rationale:**
- Strong consistency requirements (money, moderation)
- Complex relationships (users, listings, enquiries, logs)
- ACID transactions essential
- Mature ecosystem and tooling

### 4. Synchronous vs Asynchronous Processing
**Decision:** Synchronous for core flows, async for notifications
**Rationale:**
- Immediate feedback for user actions (create listing, submit enquiry)
- Async for non-critical tasks (emails, analytics)
- Simpler error handling and debugging

### 5. Build vs Buy (Moderation)
**Decision:** Build custom moderation workflow
**Rationale:**
- Domain-specific requirements (adult content)
- Need full control over workflow
- Third-party services may not support adult content
- Custom solution more cost-effective at scale

## What I Would Do Differently with More Time

1. **Comprehensive Test Suite**
   - Unit tests for all service methods
   - Feature tests for critical user flows
   - Policy tests for authorization
   - Current coverage: ~0% (focused on architecture)

2. **Media Upload System**
   - Image upload with validation
   - Image optimization (resize, compress)
   - CDN integration (S3 + CloudFront)
   - Multiple images per listing

3. **Email Notifications**
   - Welcome emails
   - Listing status change notifications
   - Enquiry notifications to providers
   - Admin alerts for moderation queue

4. **Advanced Spam Detection**
   - Train ML model on historical spam data
   - Integrate with third-party services (Akismet)
   - Implement CAPTCHA for high-risk submissions

5. **Performance Monitoring**
   - APM integration (New Relic, Datadog)
   - Query performance tracking
   - Error tracking (Sentry)
   - User analytics (Mixpanel)

6. **Admin Dashboard**
   - Analytics and reporting
   - Bulk moderation actions
   - User management
   - System health monitoring

## Time Spent

- **Database Design & Migrations:** 1.5 hours
- **Models & Business Logic:** 2 hours
- **Services & Controllers:** 2.5 hours
- **Views & Frontend:** 2 hours
- **Documentation:** 1.5 hours
- **Total:** ~9.5 hours

## Conclusion

This implementation demonstrates:
- ✅ Clean architecture with proper separation of concerns
- ✅ Production-ready code quality
- ✅ Thoughtful domain modeling
- ✅ Scalability awareness and evolution path
- ✅ Abuse prevention and moderation thinking
- ✅ SEO-first approach
- ✅ Clear communication of trade-offs

