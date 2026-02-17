@extends('layouts.app')

@section('title', $listing->meta_title ?? $listing->title)
@section('meta_description', $listing->meta_description ?? Str::limit($listing->description, 160))

@section('meta_tags')
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="{{ $listing->title }}">
    <meta property="og:description" content="{{ Str::limit($listing->description, 200) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('listings.show', $listing) }}">
@endsection

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('listings.index') }}">Listings</a></li>
            <li class="breadcrumb-item active">{{ $listing->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title">{{ $listing->title }}</h1>
                    
                    <div class="mb-3">
                        <span class="badge bg-secondary me-2">{{ ucfirst($listing->category) }}</span>
                        <span class="text-muted">
                            <i class="bi bi-geo-alt"></i> {{ $listing->city }}, {{ $listing->suburb }}
                        </span>
                    </div>

                    <div class="mb-4">
                        <h3 class="h4 text-primary">{{ $listing->formatted_price }}</h3>
                    </div>

                    <div class="mb-4">
                        <h2 class="h5">Description</h2>
                        <p class="text-break">{!! nl2br(e($listing->description)) !!}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">
                            Posted {{ $listing->created_at->diffForHumans() }} | 
                            {{ number_format($listing->view_count) }} views
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Contact Card -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Contact Provider</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Interested in this listing? Send an enquiry to the provider.</p>
                    <a href="{{ route('enquiries.create', $listing) }}" class="btn btn-primary w-100">
                        Send Enquiry
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Provider Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Provider:</strong> {{ $listing->user->name }}</p>
                    <p class="mb-0 text-muted small">Member since {{ $listing->user->created_at->format('M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <h3>More in {{ ucfirst($listing->category) }}</h3>
            <p class="text-muted">
                <a href="{{ route('listings.index', ['category' => $listing->category]) }}">
                    View all {{ ucfirst($listing->category) }} listings
                </a>
            </p>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    
    if (typeof console !== 'undefined') {
        console.log('Listing viewed:', '{{ $listing->title }}');
    }
</script>
@endpush

