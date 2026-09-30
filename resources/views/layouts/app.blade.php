<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- Dynamic Title & Meta Description per page via yield --}}
    <title>@yield('title', 'BlogHub — Modern Publishing Platform & Deep Reads')</title>
    <meta name="description" content="@yield('meta_description', 'BlogHub is an editorial publishing platform featuring long-form essays, tech insights, and thought-provoking analysis across multiple disciplines.')">
 
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">

    <!-- Preconnect to CDN origins for faster DNS/TLS negotiation -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://picsum.photos">
    <link rel="preconnect" href="https://i.pravatar.cc">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://picsum.photos">
    <link rel="dns-prefetch" href="https://i.pravatar.cc">

    <!-- Preload critical CSS -->
    <link rel="preload" href="{{ asset('assets/css/style.css') }}" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style" crossorigin>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Design System CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dark-mode.css') }}">

    @stack('styles')
</head>
<body>
   
    <!-- 1. Top Viewport Scroll Progress Bar -->
    <div class="bh-scroll-progress-container" aria-hidden="true">
        <div class="bh-scroll-progress-bar"></div>
    </div>

    <!-- 2. Sticky Navbar Partial -->
    @include('partials.navbar')

    <!-- 3. Global Search Overlay with Live Suggestions -->
    @include('partials.search-overlay')

    <!-- 4. Semantic Main Landmark -->
    <main id="main-content">
        @yield('content')
    </main>

    <!-- 5. Toast Notification Container -->
    <div class="bh-toast-container" id="toastContainer" aria-live="polite" aria-atomic="true"></div>

    <!-- 6. Floating Back to Top Button -->
    <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon bh-back-to-top" id="btnBackToTop" data-action="back-to-top" aria-label="Scroll back to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <!-- 7. Footer Partial -->
    @if (!request()->is('login') && !request()->is('register'))
        @include('partials.footer')
    @endif

    <!-- Bootstrap 5 JS Bundle CDN (deferred) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous" defer></script>

    <!-- Reusable Component Generators (renderBlogCard, authors, categories) (deferred) -->
    <script src="{{ asset('assets/js/components.js') }}" defer></script>

    <!-- Global Application Scripts (Theme Toggle, Live Search Suggestions, Scroll, Toasts) (deferred) -->
    <script src="{{ asset('assets/js/main.js') }}" defer></script>

    <!-- Toggle Actions (Like, Bookmark, Follow with optimistic UI) -->
    <script src="{{ asset('assets/js/toggle-actions.js') }}" defer></script>
    
    @stack('scripts')

    <!-- Service Worker Registration -->
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
    </script>
</body>
</html>
