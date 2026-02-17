<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Listing Platform')</title>
    
    <meta name="description" content="@yield('meta_description', 'Professional listing platform')">
    @yield('meta_tags')
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .status-badge {
            font-size: 0.875rem;
            padding: 0.25rem 0.75rem;
        }
        .listing-card {
            transition: transform 0.2s;
        }
        .listing-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">Listing Platform</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('listings.index') }}">Browse Listings</a>
                    </li>
                    @auth
                        @if(auth()->user()->isProvider())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('provider.dashboard') }}">My Dashboard</a>
                            </li>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.moderation.index') }}">Moderation</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                                <span class="badge bg-secondary">{{ ucfirst(auth()->user()->role) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('test.logout') }}">Logout</a></li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <main class="py-4">
        @yield('content')
    </main>

    <footer class="bg-light py-4 mt-5">
        <div class="container text-center text-muted">
            <p>&copy; {{ date('Y') }} Listing Platform. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>

