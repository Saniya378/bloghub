<header class="sticky-top bh-navbar">
    <div class="container-fluid px-lg-5 px-3">
        <nav class="navbar navbar-expand-lg py-3" aria-label="Main Navigation">
            <!-- Brand Logo -->
            <a class="navbar-brand d-flex align-items-center gap-2 me-4" href="{{ url('/') }}" style="text-decoration: none;">
                <span class="d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: var(--color-primary); color: #FFFFFF; border-radius: var(--border-radius); font-family: var(--font-heading); font-weight: 700; font-size: 1.25rem;">
                    B
                </span>
                <span style="font-family: var(--font-heading); font-size: 1.5rem; font-weight: 800; color: var(--color-primary); letter-spacing: -0.03em;">
                    Blog<span style="color: var(--color-accent);">Hub</span>
                </span>
            </a>

            <!-- Mobile Action Cluster (Search, Theme Toggle & Hamburger) -->
            

            <!-- Desktop Navigation Links -->
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                    <li class="nav-item">
                        <a class="bh-nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="bh-nav-link {{ request()->is('blogs*') ? 'active' : '' }}" href="{{ url('/blogs') }}">
                            Blogs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="bh-nav-link {{ request()->is('categories*') ? 'active' : '' }}" href="{{ url('/categories') }}">
                            Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="bh-nav-link {{ request()->is('authors*') ? 'active' : '' }}" href="{{ url('/authors') }}">
                            Authors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="bh-nav-link {{ request()->is('about*') ? 'active' : '' }}" href="{{ url('/about') }}">
                            About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="bh-nav-link {{ request()->is('contact*') ? 'active' : '' }}" href="{{ url('/contact') }}">
                            Contact
                        </a>
                    </li>
                </ul>

                <!-- Desktop Right Actions: Search, Theme Toggle, Auth -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Search Icon Trigger -->
                    <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" data-bs-toggle="modal" data-bs-target="#searchOverlayModal" aria-label="Search articles" title="Search articles">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <!-- Dark Mode Toggle Button -->
                    <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" data-toggle="theme" aria-label="Toggle dark mode" title="Toggle dark mode">
                        <i class="fa-solid fa-moon"></i>
                    </button>

                    <!-- Authentication Links -->
                    @auth
                    <div class="dropdown">
                        <button class="btn-bh btn-bh-ghost dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="min-height: 44px;">
                            @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background: var(--color-primary); color: white; font-weight: 700; font-size: 0.875rem;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(auth()->user()->role !== 'reader')
                            <li><a class="dropdown-item" href="{{ auth()->user()->dashboardUrl() }}"><i class="fa-solid fa-gauge me-2"></i>Dashboard</a></li>
                            @endif
                            <li><a class="dropdown-item {{ request()->is('favorites*') ? 'active' : '' }}" href="{{ route('favorites.index') }}"><i class="fa-solid fa-heart me-2"></i>My Favourites</a></li>
                            <li><a class="dropdown-item" href="{{ url('/bookmarks') }}"><i class="fa-solid fa-bookmark me-2"></i>Bookmarks</a></li>
                            <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="fa-solid fa-user me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <a href="{{ url('/login') }}" class="btn-bh btn-bh-ghost" style="min-height: 44px;">
                        Log In
                    </a>
                    <a href="{{ url('/register') }}" class="btn-bh btn-bh-primary" style="min-height: 44px;">
                        Get Started
                    </a>
                    @endauth
                </div>
            </div>
        </nav>
    </div>

    <!-- Mobile Offcanvas Menu Drawer -->
    <div class="offcanvas offcanvas-end bh-offcanvas" id="mobileNavOffcanvas" tabindex="-1" aria-labelledby="mobileNavOffcanvasLabel">
        <div class="offcanvas-header bh-offcanvas-header">
            <div class="d-flex align-items-center gap-2">
                <span class="d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: var(--color-primary); color: #FFFFFF; border-radius: var(--border-radius); font-family: var(--font-heading); font-weight: 700;">
                    B
                </span>
                <span id="mobileNavOffcanvasLabel" style="font-family: var(--font-heading); font-size: 1.25rem; font-weight: 800; color: var(--color-primary);">
                    Blog<span style="color: var(--color-accent);">Hub</span>
                </span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
        </div>

        <div class="offcanvas-body bh-offcanvas-body d-flex flex-column justify-content-between">
            <div class="d-flex flex-column gap-2">
                <!-- Mobile Search Trigger -->
                <button type="button" class="btn-bh btn-bh-ghost w-100 justify-content-start mb-3" data-bs-toggle="modal" data-bs-target="#searchOverlayModal" data-bs-dismiss="offcanvas" style="border: 1px solid var(--color-border); color: var(--color-text-muted);">
                    <i class="fa-solid fa-magnifying-glass me-2" style="color: var(--color-accent);"></i> Search articles...
                </button>

                <!-- Navigation Links with Route Active Indicators -->
                <a class="bh-offcanvas-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                    <span><i class="fa-solid fa-house me-2"></i>Home</span>
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                <a class="bh-offcanvas-link {{ request()->is('blogs*') ? 'active' : '' }}" href="{{ url('/blogs') }}">
                    <span><i class="fa-solid fa-newspaper me-2"></i>Blogs</span>
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                <a class="bh-offcanvas-link {{ request()->is('categories*') ? 'active' : '' }}" href="{{ url('/categories') }}">
                    <span><i class="fa-solid fa-shapes me-2"></i>Categories</span>
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                <a class="bh-offcanvas-link {{ request()->is('authors*') ? 'active' : '' }}" href="{{ url('/authors') }}">
                    <span><i class="fa-solid fa-users me-2"></i>Authors</span>
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                <a class="bh-offcanvas-link {{ request()->is('about*') ? 'active' : '' }}" href="{{ url('/about') }}">
                    <span><i class="fa-solid fa-compass me-2"></i>About</span>
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                <a class="bh-offcanvas-link {{ request()->is('contact*') ? 'active' : '' }}" href="{{ url('/contact') }}">
                    <span><i class="fa-solid fa-envelope me-2"></i>Contact</span>
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                @auth
                <a class="bh-offcanvas-link {{ request()->is('favorites*') ? 'active' : '' }}" href="{{ route('favorites.index') }}">
                    <span><i class="fa-solid fa-heart me-2"></i>My Favourites</span>
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                <a class="bh-offcanvas-link {{ request()->is('bookmarks*') ? 'active' : '' }}" href="{{ url('/bookmarks') }}">
                    <span><i class="fa-solid fa-bookmark me-2"></i>Bookmarks</span>
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                @if(auth()->user()->role !== 'reader')
                <a class="bh-offcanvas-link {{ request()->is('dashboard*') || request()->is('admin*') ? 'active' : '' }}" href="{{ auth()->user()->dashboardUrl() }}">
                    <span><i class="fa-solid fa-gauge me-2"></i>Dashboard</span>
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                @endif
                @endauth
            </div>

            <!-- Offcanvas Auth & Mode Footer -->
            <div class="pt-4 mt-4" style="border-top: 1px solid var(--color-border-subtle);">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="bh-caption fw-semibold">APPEARANCE</span>
                    <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" data-toggle="theme" aria-label="Toggle dark mode">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                </div>
                <div class="d-flex flex-column gap-2">
                    @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-bh btn-bh-ghost w-100">
                            Log Out
                        </button>
                    </form>
                    @else
                    <a href="{{ url('/login') }}" class="btn-bh btn-bh-ghost w-100">
                        Log In
                    </a>
                    <a href="{{ url('/register') }}" class="btn-bh btn-bh-primary w-100">
                        Get Started
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>
