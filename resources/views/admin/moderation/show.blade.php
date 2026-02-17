@extends('layouts.app')

@section('title', 'Review Listing - ' . $listing->title)

@section('content')
<div class="container">
    <div class="row">
        <!-- Listing Content -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Listing Details</h4>
                </div>
                <div class="card-body">
                    <h2>{{ $listing->title }}</h2>
                    
                    <div class="mb-3">
                        <span class="badge bg-secondary me-2">{{ ucfirst($listing->category) }}</span>
                        <span class="badge bg-warning text-dark">{{ ucfirst(str_replace('_', ' ', $listing->status)) }}</span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Location:</strong> {{ $listing->city }}, {{ $listing->suburb }}
                        </div>
                        <div class="col-md-6">
                            <strong>Price:</strong> {{ $listing->formatted_price }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Description:</strong>
                        <p class="mt-2">{!! nl2br(e($listing->description)) !!}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>Provider:</strong> {{ $listing->user->name }}<br>
                            <small class="text-muted">{{ $listing->user->email }}</small>
                        </div>
                        <div class="col-md-6">
                            <strong>Submitted:</strong> {{ $listing->submitted_at->format('M d, Y H:i') }}<br>
                            <small class="text-muted">{{ $listing->submitted_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Moderation History -->
            @if($listing->moderationLogs->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Moderation History</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($listing->moderationLogs as $log)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ ucfirst($log->action) }}</h6>
                                        <small>{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">
                                        <strong>From:</strong> {{ ucfirst(str_replace('_', ' ', $log->from_status ?? 'N/A')) }} 
                                        <strong>To:</strong> {{ ucfirst(str_replace('_', ' ', $log->to_status)) }}
                                    </p>
                                    @if($log->user)
                                        <small class="text-muted">By: {{ $log->user->name }}</small>
                                    @endif
                                    @if($log->reason)
                                        <p class="mb-1"><strong>Reason:</strong> {{ $log->reason }}</p>
                                    @endif
                                    @if($log->notes)
                                        <p class="mb-0"><strong>Notes:</strong> {{ $log->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Approve Listing</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.moderation.approve', $listing) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="approve_notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="approve_notes" name="notes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Approve</button>
                    </form>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Reject Listing</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.moderation.reject', $listing) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="reject_reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reject_reason" name="reason" rows="3" required></textarea>
                            <small class="form-text text-muted">This will be shown to the provider.</small>
                        </div>
                        <div class="mb-3">
                            <label for="reject_notes" class="form-label">Internal Notes (Optional)</label>
                            <textarea class="form-control" id="reject_notes" name="notes" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Reject</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Suspend Listing</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.moderation.suspend', $listing) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="suspend_reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="suspend_reason" name="reason" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="suspend_notes" class="form-label">Internal Notes (Optional)</label>
                            <textarea class="form-control" id="suspend_notes" name="notes" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Suspend</button>
                    </form>
                </div>
            </div>

            <div class="mt-3">
                <a href="{{ route('admin.moderation.index') }}" class="btn btn-outline-secondary w-100">
                    Back to Queue
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

