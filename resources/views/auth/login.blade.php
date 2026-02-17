@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Quick Login (Testing)</h4>
                </div>
                <div class="card-body">
                    
                    <div class="mb-4">
                        <h5>Login as Admin</h5>
                        <a href="{{ route('test.login', 'admin@example.com') }}" class="btn btn-danger btn-lg w-100">
                            <i class="bi bi-shield-lock"></i> Login as Admin
                        </a>
                        <small class="text-muted">Access: Moderation Queue, Approve/Reject Listings</small>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h5>Login as Provider</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('test.login', 'sarah@example.com') }}" class="btn btn-success w-100">
                                    Sarah Johnson
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('test.login', 'emma@example.com') }}" class="btn btn-success w-100">
                                    Emma Wilson
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('test.login', 'olivia@example.com') }}" class="btn btn-success w-100">
                                    Olivia Brown
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('test.login', 'ava@example.com') }}" class="btn btn-success w-100">
                                    Ava Davis
                                </a>
                            </div>
                        </div>
                        <small class="text-muted">Access: Provider Dashboard, Create/Edit Listings</small>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h5>Login as Guest</h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('test.login', 'guest1@example.com') }}" class="btn btn-info w-100">
                                    Guest User 1
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('test.login', 'guest2@example.com') }}" class="btn btn-info w-100">
                                    Guest User 2
                                </a>
                            </div>
                        </div>
                        <small class="text-muted">Access: Browse Listings, Submit Enquiries</small>
                    </div>

                    <hr>

                    <div class="alert alert-info">
                        <strong>Note:</strong> All test accounts use the password: <code>password</code>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('listings.index') }}" class="btn btn-outline-secondary">
                            Browse Listings
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

