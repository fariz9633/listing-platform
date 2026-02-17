@extends('layouts.app')

@section('title', 'Send Enquiry - ' . $listing->title)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">{{ $listing->title }}</h5>
                    <p class="text-muted mb-0">
                        <i class="bi bi-geo-alt"></i> {{ $listing->city }}, {{ $listing->suburb }} | 
                        <strong class="text-primary">{{ $listing->formatted_price }}</strong>
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Send Enquiry</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enquiries.store', $listing) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Your Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Your email will not be shared publicly.</small>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number (Optional)</label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      name="message" 
                                      rows="6" 
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Minimum 10 characters, maximum 2000 characters.</small>
                        </div>

                        <div class="alert alert-info">
                            <strong>Note:</strong> Your enquiry will be sent directly to the provider. 
                            Please do not include sensitive personal information in your message.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Send Enquiry</button>
                            <a href="{{ route('listings.show', $listing) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning mt-3">
                <small>
                    <strong>Rate Limiting:</strong> To prevent spam, we limit the number of enquiries per IP address and email. 
                    If you've reached the limit, please try again later.
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

