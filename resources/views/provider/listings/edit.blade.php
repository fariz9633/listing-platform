@extends('layouts.app')

@section('title', 'Edit Listing')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="mb-4">Edit Listing</h1>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('provider.listings.update', $listing) }}">
                        @csrf
                        @method('PUT')
                        @include('provider.listings.form')
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Update Listing</button>
                            <a href="{{ route('provider.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

