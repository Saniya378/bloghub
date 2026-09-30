<!DOCTYPE html>
<html lang="en" data-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — BlogHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dark-mode.css') }}">
    <style>
        body.dashboard-body {
            margin: 0;
            padding: 0;
            font-family: var(--font-body, system-ui, sans-serif);
            background-color: var(--color-bg, #f8f9fa);
            color: var(--color-text, #212529);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
            position: relative;
            background-color: var(--color-bg, #f8f9fa);
        }
        .dashboard-sidebar {
            width: 260px;
            min-width: 260px;
            max-width: 260px;
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
            flex-shrink: 0;
            z-index: 1040;
            background-color: var(--color-surface, #ffffff);
            border-color: var(--color-border, #e0e0e0) !important;
            transition: transform 0.3s ease-in-out, background-color 0.3s ease, border-color 0.3s ease;
        }
        .dashboard-sidebar .border-bottom {
            border-color: var(--color-border, #e0e0e0) !important;
        }
        .dashboard-content {
            flex: 1 1 auto;
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--color-bg, #f8f9fa);
            transition: background-color 0.3s ease;
        }
        .dashboard-main {
            flex-grow: 1;
            min-width: 0;
            overflow-x: auto;
            background-color: var(--color-bg, #f8f9fa);
        }
        .dashboard-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            background-color: var(--color-surface, #ffffff) !important;
            border-color: var(--color-border, #e0e0e0) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1035;
        }
        @media (max-width: 991.98px) {
            .dashboard-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                transform: translateX(-100%);
            }
            .dashboard-sidebar.show {
                transform: translateX(0);
            }
            .sidebar-backdrop.show {
                display: block;
            }
            .dashboard-content {
                width: 100%;
            }
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: var(--color-text-muted, #575E6C);
            text-decoration: none;
            transition: all 0.2s;
            border-radius: var(--border-radius, 6px);
        }
        .sidebar-link:hover, .sidebar-link.active {
            color: var(--color-primary, #1F2A44);
            background: var(--color-primary-subtle, rgba(31, 42, 68, 0.08));
        }
        .sidebar-link i {
            width: 20px;
            text-align: center;
        }

        /* Dark Mode Dashboard Overrides */
        [data-theme="dark"] .dashboard-sidebar {
            background-color: var(--color-surface, #161D2A) !important;
            border-color: var(--color-border, #2B374D) !important;
        }
        [data-theme="dark"] .dashboard-topbar {
            background-color: var(--color-surface, #161D2A) !important;
            border-color: var(--color-border, #2B374D) !important;
            color: var(--color-text, #F1F4FA);
        }
        [data-theme="dark"] .dashboard-topbar .btn-link,
        [data-theme="dark"] .dashboard-topbar .dropdown-toggle {
            color: var(--color-text, #F1F4FA) !important;
        }
        [data-theme="dark"] .sidebar-link {
            color: var(--color-text-muted, #9DA7B8);
        }
        [data-theme="dark"] .sidebar-link:hover,
        [data-theme="dark"] .sidebar-link.active {
            color: var(--color-text, #F1F4FA);
            background-color: var(--color-surface-subtle, #1F2839);
        }
        [data-theme="dark"] .card {
            background-color: var(--color-card-bg, #161D2A) !important;
            border-color: var(--color-border, #2B374D) !important;
            color: var(--color-text, #F1F4FA) !important;
        }
        [data-theme="dark"] .card-header,
        [data-theme="dark"] .card-footer {
            background-color: var(--color-surface-subtle, #1F2839) !important;
            border-color: var(--color-border, #2B374D) !important;
            color: var(--color-text, #F1F4FA) !important;
        }
        [data-theme="dark"] .text-dark {
            color: var(--color-text, #F1F4FA) !important;
        }
        [data-theme="dark"] .text-muted {
            color: var(--color-text-muted, #9DA7B8) !important;
        }
        [data-theme="dark"] .bg-light {
            background-color: var(--color-surface-subtle, #1F2839) !important;
            color: var(--color-text, #F1F4FA) !important;
        }
        [data-theme="dark"] .table {
            color: var(--color-text, #F1F4FA) !important;
            border-color: var(--color-border, #2B374D) !important;
        }
        [data-theme="dark"] .table th,
        [data-theme="dark"] .table td {
            background-color: transparent !important;
            color: var(--color-text, #F1F4FA) !important;
            border-color: var(--color-border, #2B374D) !important;
        }
        [data-theme="dark"] .table-hover > tbody > tr:hover > * {
            background-color: var(--color-surface-subtle, #1F2839) !important;
            color: var(--color-text, #F1F4FA) !important;
        }
    </style>
</head>
<body class="dashboard-body">
    <div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar border-end" id="sidebar">
        <div class="p-4 border-bottom">
            <a href="{{ url('/') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <span class="d-inline-flex align-items-center justify-content-center rounded" style="width: 36px; height: 36px; background: var(--color-primary); color: white; font-weight: 700;">B</span>
                <span class="fw-bold fs-5" style="color: var(--color-primary);">BlogHub</span>
            </a>
        </div>
        <nav class="p-3">
            @if(auth()->user()->hasRole('admin'))
            <div class="small text-muted fw-semibold px-3 mb-2">ADMIN</div>
            <a href="{{ route('admin.index') }}" class="sidebar-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Overview
            </a>
            <a href="{{ route('admin.users') }}" class="sidebar-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Users
            </a>
            <a href="{{ route('admin.posts') }}" class="sidebar-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i> Posts
            </a>
            <a href="{{ route('admin.categories') }}" class="sidebar-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <i class="fa-solid fa-folder-open"></i> Categories
            </a>
            <a href="{{ route('admin.comments') }}" class="sidebar-link {{ request()->routeIs('admin.comments*') ? 'active' : '' }}">
                <i class="fa-solid fa-comments"></i> Comments
            </a>
            <a href="{{ route('admin.testimonials') }}" class="sidebar-link {{ request()->routeIs('admin.testimonials*') ? 'active' : '' }}">
                <i class="fa-solid fa-quote-left"></i> Testimonials
            </a>
            <a href="{{ route('admin.messages') }}" class="sidebar-link {{ request()->routeIs('admin.messages*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope"></i> Messages
                @php $unread = \App\Models\ContactMessage::where('is_read', false)->count(); @endphp
                @if($unread > 0)
                <span class="badge bg-danger rounded-pill ms-auto">{{ $unread }}</span>
                @endif
            </a>
            <a href="{{ route('admin.subscribers') }}" class="sidebar-link {{ request()->routeIs('admin.subscribers*') ? 'active' : '' }}">
                <i class="fa-solid fa-newspaper"></i> Subscribers
            </a>
            @else
            <div class="small text-muted fw-semibold px-3 mb-2">MAIN</div>
            <a href="{{ url('/dashboard') }}" class="sidebar-link {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i> Overview
            </a>
            <a href="{{ url('/dashboard/posts') }}" class="sidebar-link {{ request()->is('dashboard/posts*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i> My Posts
            </a>
            <a href="{{ url('/dashboard/posts/create') }}" class="sidebar-link {{ request()->is('dashboard/posts/create') ? 'active' : '' }}">
                <i class="fa-solid fa-plus"></i> New Post
            </a>
            <a href="{{ url('/dashboard/comments') }}" class="sidebar-link {{ request()->is('dashboard/comments*') ? 'active' : '' }}">
                <i class="fa-solid fa-comments"></i> Comments
            </a>
            @endif
            <div class="small text-muted fw-semibold px-3 mt-4 mb-2">ACCOUNT</div>
            <a href="{{ url('/profile') }}" class="sidebar-link">
                <i class="fa-solid fa-user"></i> Profile Settings
            </a>
            <a href="{{ url('/bookmarks') }}" class="sidebar-link">
                <i class="fa-solid fa-bookmark"></i> My Bookmarks
            </a>
            <a href="{{ url('/') }}" class="sidebar-link">
                <i class="fa-solid fa-globe"></i> View Site
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="sidebar-link w-100 border-0 bg-transparent">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <!-- Backdrop for mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Main Content -->
    <div class="dashboard-content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand-lg border-bottom dashboard-topbar">
            <div class="container-fluid">
                <button class="btn btn-link d-lg-none" id="sidebarToggle"><i class="fa-solid fa-bars fa-lg"></i></button>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <button class="btn btn-link" data-toggle="theme"><i class="fa-solid fa-moon"></i></button>
                    <div class="dropdown">
                        <button class="btn btn-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                            @else
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: var(--color-primary); color: white; font-weight: 700;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            @endif
                            <span class="d-none d-md-inline fw-semibold">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text fw-semibold">{{ auth()->user()->name }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ url('/profile') }}">Profile</a></li>
                            <li><a class="dropdown-item" href="{{ url('/') }}">View Site</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="p-4 dashboard-main">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('content')
        </main>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle on mobile
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        });
        backdrop?.addEventListener('click', () => {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });

        // Dark mode toggle
        document.querySelector('[data-toggle="theme"]')?.addEventListener('click', () => {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('bloghub_theme', next);
        });

        // Restore theme
        const saved = localStorage.getItem('bloghub_theme');
        if (saved) document.documentElement.setAttribute('data-theme', saved);
    </script>
    @stack('scripts')
</body>
</html>
