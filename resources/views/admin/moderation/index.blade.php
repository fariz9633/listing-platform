@extends('layouts.app')

@section('title', 'Moderation Queue')

@section('content')
<div class="container">
    <h1 class="mb-4">Moderation Queue</h1>

    @if($listings->count() > 0)
        <div class="alert alert-info">
            <strong>{{ $listings->total() }}</strong> listing(s) pending moderation
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Submitted</th>
                        <th>Title</th>
                        <th>Provider</th>
                        <th>Category</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listings as $listing)
                        <tr>
                            <td>
                                <small>{{ $listing->submitted_at->diffForHumans() }}</small>
                                <br>
                                <small class="text-muted">{{ $listing->submitted_at->format('M d, Y H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $listing->title }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($listing->description, 80) }}</small>
                            </td>
                            <td>
                                {{ $listing->user->name }}
                                <br>
                                <small class="text-muted">{{ $listing->user->email }}</small>
                            </td>
                            <td>{{ ucfirst($listing->category) }}</td>
                            <td>{{ $listing->city }}, {{ $listing->suburb }}</td>
                            <td>
                                <a href="{{ route('admin.moderation.show', $listing) }}" 
                                   class="btn btn-sm btn-primary">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        
        <div class="d-flex justify-content-center mt-4">
            {{ $listings->links() }}
        </div>
    @else
        <div class="alert alert-success">
            <h4>All caught up!</h4>
            <p>There are no listings pending moderation at this time.</p>
        </div>
    @endif
</div>
@endsection

