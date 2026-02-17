# How to Access Admin, Provider, and Guest Pages

## 🚨 Important: Authentication Required

The provider and admin routes require authentication. You need to install Laravel authentication first.

## Quick Setup: Install Laravel Breeze (Recommended)

### Step 1: Install Laravel Breeze

```bash
cd listing-platform
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
php artisan migrate
```

**Note:** If you already ran migrations, the last command will just add auth tables.

### Step 2: Seed the Database (if not done already)

```bash
php artisan migrate:fresh --seed
```

## 📍 All Available URLs

### Public Pages (No Login Required)

| Page | URL | Description |
|------|-----|-------------|
| **Home/Listings** | `http://localhost:8000/` | Browse all approved listings |
| **Listings Index** | `http://localhost:8000/listings` | Same as home |
| **Listing Detail** | `http://localhost:8000/listings/{slug}` | View individual listing |
| **Send Enquiry** | `http://localhost:8000/listings/{slug}/enquire` | Contact form for a listing |

**Example:**
- `http://localhost:8000/listings`
- `http://localhost:8000/listings/professional-companion-available-sydney-abc123`

### Authentication Pages (After Installing Breeze)

| Page | URL | Description |
|------|-----|-------------|
| **Login** | `http://localhost:8000/login` | Login page |
| **Register** | `http://localhost:8000/register` | Registration page |
| **Logout** | `http://localhost:8000/logout` | Logout (POST request) |

### Provider Pages (Login Required - Provider Role)

| Page | URL | Description |
|------|-----|-------------|
| **Provider Dashboard** | `http://localhost:8000/provider/dashboard` | View all your listings |
| **Create Listing** | `http://localhost:8000/provider/listings/create` | Create new listing |
| **Edit Listing** | `http://localhost:8000/provider/listings/{id}/edit` | Edit existing listing |

**Login Credentials:**
- Email: `sarah@example.com` (or any provider email)
- Password: `password`

### Admin Pages (Login Required - Admin Role)

| Page | URL | Description |
|------|-----|-------------|
| **Moderation Queue** | `http://localhost:8000/admin/moderation` | View pending listings |
| **Review Listing** | `http://localhost:8000/admin/moderation/{id}` | Review specific listing |

**Login Credentials:**
- Email: `admin@example.com`
- Password: `password`

### Guest Pages

Guests can:
- Browse all public pages (listings)
- Submit enquiries (no login required)
- Optionally login to track their enquiries

**Login Credentials:**
- Email: `guest1@example.com` (through `guest5@example.com`)
- Password: `password`

## 🎯 Quick Access After Setup

### 1. Start the Server

```bash
php artisan serve
```

### 2. Access Public Pages

Open browser: `http://localhost:8000`

### 3. Login as Admin

1. Go to: `http://localhost:8000/login`
2. Email: `admin@example.com`
3. Password: `password`
4. After login, go to: `http://localhost:8000/admin/moderation`

### 4. Login as Provider

1. Go to: `http://localhost:8000/login`
2. Email: `sarah@example.com`
3. Password: `password`
4. After login, go to: `http://localhost:8000/provider/dashboard`

### 5. Browse as Guest

Just visit: `http://localhost:8000/listings` (no login needed)

## 🔧 Alternative: Without Laravel Breeze

If you don't want to install Breeze, you can create a simple login controller manually or use Laravel UI.

### Option A: Laravel UI (Simpler)

```bash
composer require laravel/ui
php artisan ui bootstrap --auth
npm install && npm run dev
```

### Option B: Manual Login (Quick Test)

Create a simple test route to manually login (for testing only):

Add to `routes/web.php`:

```php
// TESTING ONLY - Remove in production
Route::get('/test-login/{email}', function ($email) {
    $user = \App\Models\User::where('email', $email)->first();
    if ($user) {
        Auth::login($user);
        return redirect('/provider/dashboard');
    }
    return 'User not found';
});
```

Then visit:
- `http://localhost:8000/test-login/admin@example.com` (login as admin)
- `http://localhost:8000/test-login/sarah@example.com` (login as provider)

## 📋 Complete User List (After Seeding)

### Admin Users
- admin@example.com

### Provider Users
- sarah@example.com
- emma@example.com
- olivia@example.com
- ava@example.com
- isabella@example.com
- sophia@example.com
- mia@example.com
- charlotte@example.com
- amelia@example.com
- harper@example.com

### Guest Users
- guest1@example.com
- guest2@example.com
- guest3@example.com
- guest4@example.com
- guest5@example.com

**All passwords:** `password`

## 🎨 Navigation After Login

The layout (`resources/views/layouts/app.blade.php`) includes role-based navigation:

- **Admin users** see: "Moderation Queue" link
- **Provider users** see: "My Dashboard" link
- **All users** see: "Listings" link

## ❓ Troubleshooting

### "Route [login] not defined" Error

This means authentication is not installed. Run:

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
```

### "Unauthenticated" Error

You need to login first. Go to `/login` page.

### Can't See Admin/Provider Links

Make sure:
1. You're logged in
2. Your user has the correct role (admin/provider)
3. Check the navigation in the layout file

## 🚀 Recommended Setup Flow

1. Install Laravel Breeze: `composer require laravel/breeze --dev`
2. Setup Breeze: `php artisan breeze:install blade`
3. Install NPM packages: `npm install && npm run dev`
4. Seed database: `php artisan migrate:fresh --seed`
5. Start server: `php artisan serve`
6. Visit: `http://localhost:8000/login`
7. Login with any seeded user credentials

Enjoy exploring the platform! 🎉

