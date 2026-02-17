
<div class="mb-3">
    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
    <input type="text" 
           class="form-control @error('title') is-invalid @enderror" 
           id="title" 
           name="title" 
           value="{{ old('title', $listing->title ?? '') }}" 
           required
           maxlength="255">
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
    <textarea class="form-control @error('description') is-invalid @enderror" 
              id="description" 
              name="description" 
              rows="8" 
              required
              minlength="50"
              maxlength="5000">{{ old('description', $listing->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Minimum 50 characters. Provide detailed information about your listing.</small>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control @error('category') is-invalid @enderror" 
               id="category" 
               name="category" 
               value="{{ old('category', $listing->category ?? '') }}" 
               required
               maxlength="100"
               placeholder="e.g., escort, massage, etc.">
        @error('category')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="pricing_type" class="form-label">Pricing Type <span class="text-danger">*</span></label>
        <select class="form-select @error('pricing_type') is-invalid @enderror" 
                id="pricing_type" 
                name="pricing_type" 
                required>
            <option value="hourly" {{ old('pricing_type', $listing->pricing_type ?? '') == 'hourly' ? 'selected' : '' }}>Hourly</option>
            <option value="fixed" {{ old('pricing_type', $listing->pricing_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
        </select>
        @error('pricing_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" 
               class="form-control @error('price') is-invalid @enderror" 
               id="price" 
               name="price" 
               value="{{ old('price', $listing->price ?? '') }}" 
               step="0.01"
               min="0"
               max="99999.99">
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="price_min" class="form-label">Min Price (for ranges)</label>
        <input type="number" 
               class="form-control @error('price_min') is-invalid @enderror" 
               id="price_min" 
               name="price_min" 
               value="{{ old('price_min', $listing->price_min ?? '') }}" 
               step="0.01"
               min="0"
               max="99999.99">
        @error('price_min')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="price_max" class="form-label">Max Price (for ranges)</label>
        <input type="number" 
               class="form-control @error('price_max') is-invalid @enderror" 
               id="price_max" 
               name="price_max" 
               value="{{ old('price_max', $listing->price_max ?? '') }}" 
               step="0.01"
               min="0"
               max="99999.99">
        @error('price_max')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control @error('city') is-invalid @enderror" 
               id="city" 
               name="city" 
               value="{{ old('city', $listing->city ?? '') }}" 
               required
               maxlength="100">
        @error('city')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="suburb" class="form-label">Suburb <span class="text-danger">*</span></label>
        <input type="text" 
               class="form-control @error('suburb') is-invalid @enderror" 
               id="suburb" 
               name="suburb" 
               value="{{ old('suburb', $listing->suburb ?? '') }}" 
               required
               maxlength="100">
        @error('suburb')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="meta_title" class="form-label">SEO Title (Optional)</label>
    <input type="text" 
           class="form-control @error('meta_title') is-invalid @enderror" 
           id="meta_title" 
           name="meta_title" 
           value="{{ old('meta_title', $listing->meta_title ?? '') }}" 
           maxlength="60">
    @error('meta_title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Recommended: 50-60 characters</small>
</div>

<div class="mb-4">
    <label for="meta_description" class="form-label">SEO Description (Optional)</label>
    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
              id="meta_description" 
              name="meta_description" 
              rows="3"
              maxlength="160">{{ old('meta_description', $listing->meta_description ?? '') }}</textarea>
    @error('meta_description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="form-text text-muted">Recommended: 150-160 characters</small>
</div>

