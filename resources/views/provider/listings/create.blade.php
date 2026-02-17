@extends('layouts.app')

@section('title', 'Create Listing')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="mb-4">Create New Listing</h1>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('provider.listings.store') }}">
                        @csrf
                        @include('provider.listings.form')
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Create Listing</button>
                            <a href="{{ route('provider.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

