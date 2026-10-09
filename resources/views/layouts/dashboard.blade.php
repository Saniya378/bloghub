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
        .transition-icon {
            transition: transform 0.2s ease;
        }
        [aria-expanded="true"] .transition-icon {
            transform: rotate(180deg);
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
            @php $pendingPosts = \App\Models\Article::pending()->count(); @endphp
            @if($pendingPosts > 0)
            <a href="{{ route('admin.posts', ['status' => 'pending']) }}" class="sidebar-link" style="color: var(--color-primary); font-weight: 600;">
                <i class="fa-solid fa-hourglass-half"></i> Review Requests
                <span class="badge bg-danger rounded-pill ms-auto">{{ $pendingPosts }}</span>
            </a>
            @endif
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
            <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#reportsSubmenu" aria-expanded="{{ request()->routeIs('admin.reports*') ? 'true' : 'false' }}">
                <i class="fa-solid fa-chart-line"></i> Reports
                <i class="fa-solid fa-chevron-down ms-auto small transition-icon {{ request()->routeIs('admin.reports*') ? 'rotate-icon' : '' }}"></i>
            </a>
            <div class="collapse {{ request()->routeIs('admin.reports*') ? 'show' : '' }} ps-3" id="reportsSubmenu">
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link py-1 {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}" style="font-size: 0.875rem;">
                    <i class="fa-solid fa-chart-pie"></i> Overview
                </a>
                <a href="{{ route('admin.reports.authors') }}" class="sidebar-link py-1 {{ request()->routeIs('admin.reports.authors*') ? 'active' : '' }}" style="font-size: 0.875rem;">
                    <i class="fa-solid fa-users"></i> Authors
                </a>
                <a href="{{ route('admin.reports.articles') }}" class="sidebar-link py-1 {{ request()->routeIs('admin.reports.articles*') ? 'active' : '' }}" style="font-size: 0.875rem;">
                    <i class="fa-solid fa-newspaper"></i> Articles
                </a>
                <a href="{{ route('admin.reports.approvals.articles') }}" class="sidebar-link py-1 {{ request()->routeIs('admin.reports.approvals*') ? 'active' : '' }}" style="font-size: 0.875rem;">
                    <i class="fa-solid fa-shield-halved"></i> Approvals
                </a>
                <a href="{{ route('admin.reports.engagement.views') }}" class="sidebar-link py-1 {{ request()->routeIs('admin.reports.engagement*') ? 'active' : '' }}" style="font-size: 0.875rem;">
                    <i class="fa-solid fa-arrow-trend-up"></i> Engagement
                </a>
                <a href="{{ route('admin.reports.activity') }}" class="sidebar-link py-1 {{ request()->routeIs('admin.reports.activity*') ? 'active' : '' }}" style="font-size: 0.875rem;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Activity Log
                </a>
            </div>
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
            <a href="{{ route('favorites.index') }}" class="sidebar-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}">
                <i class="fa-solid fa-heart"></i> My Favourites
            </a>
            <a href="{{ url('/bookmarks') }}" class="sidebar-link {{ request()->is('bookmarks*') ? 'active' : '' }}">
                <i class="fa-solid fa-bookmark"></i> My Bookmarks
            </a>
            @php $sidebarUnread = auth()->user()->unreadNotifications()->count(); @endphp
            <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell"></i> Notifications
                @if($sidebarUnread > 0)
                <span class="badge bg-danger rounded-pill ms-auto">{{ $sidebarUnread }}</span>
                @endif
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

                    <!-- Notifications -->
                    @php
                        $unreadCount = auth()->user()->unreadNotifications()->count();
                        $latestNotifications = auth()->user()->notifications()->latest()->take(6)->get();
                    @endphp
                    <div class="dropdown">
                        <button class="btn btn-link position-relative" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Notifications" title="Notifications">
                            <i class="fa-solid fa-bell"></i>
                            @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-0" style="min-width: 320px; max-width: 340px; max-height: 420px; overflow-y: auto;">
                            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                <span class="fw-semibold small">Notifications</span>
                                @if($unreadCount > 0)
                                <form method="POST" action="{{ route('notifications.readAll') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link btn-sm p-0 small">Mark all read</button>
                                </form>
                                @endif
                            </div>
                            @forelse($latestNotifications as $notification)
                            @php $data = $notification->data; @endphp
                            <a href="{{ $data['type'] === 'post_rejected' ? route('dashboard.posts.edit', $data['article_id']) : url('/blogs/' . $data['slug']) }}"
                                class="dropdown-item d-flex gap-2 py-2 {{ $notification->read_at ? '' : 'bg-light-subtle' }}">
                                <i class="{{ ($notification->type === App\Notifications\PostRejected::class ? App\Notifications\PostRejected::presentation() : App\Notifications\PostApproved::presentation())['icon'] }} {{ ($notification->type === App\Notifications\PostRejected::class ? App\Notifications\PostRejected::presentation() : App\Notifications\PostApproved::presentation())['color'] }} mt-1"></i>
                                <span class="small">
                                    <span class="d-block">{{ Str::limit($data['message'] ?? 'Notification', 80) }}</span>
                                    <span class="text-muted d-block" style="font-size: 0.75rem;"><x-time-ago :date="$notification->created_at" /></span>
                                </span>
                            </a>
                            @empty
                            <div class="px-3 py-4 text-center text-muted small">
                                <i class="fa-regular fa-bell-slash d-block mb-2 fs-5"></i>
                                No notifications yet.
                            </div>
                            @endforelse
                            <div class="border-top px-3 py-2 text-center">
                                <a href="{{ route('notifications.index') }}" class="small text-decoration-none">View all notifications</a>
                            </div>
                        </div>
                    </div>
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
    {{-- Live relative timestamps ("just now", "1 sec ago") --}}
    <script>
        window.blogHubServerTime = @json(now()->timestamp);
    </script>
    <script src="{{ asset('assets/js/relative-time.js') }}"></script>
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
