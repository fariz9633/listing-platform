@extends('layouts.app')

@section('title', 'Provider Dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Listings</h1>
        <a href="{{ route('provider.listings.create') }}" class="btn btn-primary">
            Create New Listing
        </a>
    </div>

    @if($listings->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Enquiries</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listings as $listing)
                        <tr>
                            <td>
                                <strong>{{ $listing->title }}</strong>
                                <br>
                                <small class="text-muted">Created {{ $listing->created_at->diffForHumans() }}</small>
                            </td>
                            <td>{{ ucfirst($listing->category) }}</td>
                            <td>{{ $listing->city }}</td>
                            <td>{{ $listing->formatted_price }}</td>
                            <td>
                                @if($listing->status === 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($listing->status === 'pending_moderation')
                                    <span class="badge bg-warning text-dark">Pending Review</span>
                                @elseif($listing->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($listing->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($listing->status === 'suspended')
                                    <span class="badge bg-dark">Suspended</span>
                                @endif
                            </td>
                            <td>{{ number_format($listing->view_count) }}</td>
                            <td>{{ number_format($listing->enquiry_count) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    @if($listing->isApproved())
                                        <a href="{{ route('listings.show', $listing) }}" 
                                           class="btn btn-outline-primary" 
                                           target="_blank">View</a>
                                    @endif
                                    
                                    @can('update', $listing)
                                        <a href="{{ route('provider.listings.edit', $listing) }}" 
                                           class="btn btn-outline-secondary">Edit</a>
                                    @endcan
                                    
                                    @can('submit', $listing)
                                        <form method="POST" 
                                              action="{{ route('provider.listings.submit', $listing) }}" 
                                              class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success">Submit</button>
                                        </form>
                                    @endcan
                                    
                                    @can('delete', $listing)
                                        <form method="POST" 
                                              action="{{ route('provider.listings.destroy', $listing) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this listing?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">Delete</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        
                        @if($listing->isRejected() && $listing->rejection_reason)
                            <tr>
                                <td colspan="8" class="bg-light">
                                    <div class="alert alert-danger mb-0">
                                        <strong>Rejection Reason:</strong> {{ $listing->rejection_reason }}
                                        @if($listing->moderation_notes)
                                            <br><strong>Notes:</strong> {{ $listing->moderation_notes }}
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $listings->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <h4>No listings yet</h4>
            <p>You haven't created any listings. Click the button above to create your first listing.</p>
        </div>
    @endif
</div>
@endsection

