@extends('layouts.app')

@section('title', 'Browse Listings')
@section('meta_description', 'Browse and search professional listings')

@section('content')
<div class="container">
    <div class="row">
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Filter Results</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('listings.index') }}">
                        <div class="mb-3">
                            <label for="q" class="form-label">Keyword</label>
                            <input type="text" class="form-control" id="q" name="q" 
                                   value="{{ $filters['keyword'] ?? '' }}" 
                                   placeholder="Search...">
                        </div>

                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach($filterOptions['categories'] as $cat)
                                    <option value="{{ $cat['category'] }}" 
                                            {{ ($filters['category'] ?? '') == $cat['category'] ? 'selected' : '' }}>
                                        {{ ucfirst($cat['category']) }} ({{ $cat['count'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <select class="form-select" id="city" name="city">
                                <option value="">All Cities</option>
                                @foreach($filterOptions['cities'] as $cityData)
                                    <option value="{{ $cityData['city'] }}" 
                                            {{ ($filters['city'] ?? '') == $cityData['city'] ? 'selected' : '' }}>
                                        {{ $cityData['city'] }} ({{ $cityData['count'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="suburb" class="form-label">Suburb</label>
                            <input type="text" class="form-control" id="suburb" name="suburb" 
                                   value="{{ $filters['suburb'] ?? '' }}" 
                                   placeholder="Enter suburb">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="price_min" 
                                           value="{{ $filters['price_min'] ?? '' }}" 
                                           placeholder="Min">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="price_max" 
                                           value="{{ $filters['price_max'] ?? '' }}" 
                                           placeholder="Max">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ ($filters['sort'] ?? 'newest') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="price_low" {{ ($filters['sort'] ?? '') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ ($filters['sort'] ?? '') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        <a href="{{ route('listings.index') }}" class="btn btn-outline-secondary w-100 mt-2">Clear Filters</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Listings</h2>
                <p class="text-muted mb-0">{{ $listings->total() }} results</p>
            </div>

            @if($listings->count() > 0)
                <div class="row">
                    @foreach($listings as $listing)
                        <div class="col-md-4 mb-4">
                            <div class="card listing-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('listings.show', $listing) }}" class="text-decoration-none text-dark">
                                            {{ $listing->title }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small">
                                        <i class="bi bi-geo-alt"></i> {{ $listing->city }}, {{ $listing->suburb }}
                                    </p>
                                    <p class="card-text">{{ Str::limit($listing->description, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-secondary">{{ ucfirst($listing->category) }}</span>
                                        <strong class="text-primary">{{ $listing->formatted_price }}</strong>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="{{ route('listings.show', $listing) }}" class="btn btn-sm btn-outline-primary w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $listings->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    No listings found matching your criteria. Try adjusting your filters.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

