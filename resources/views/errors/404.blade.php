<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found — BlogHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dark-mode.css') }}">
    <style>
        .bh-404-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-8) 0;
            background-color: var(--color-bg);
        }

        .bh-404-content {
            text-align: center;
            max-width: 560px;
            padding: 0 var(--space-4);
        }

        .bh-404-graphic {
            margin-bottom: var(--space-6);
        }

        .bh-404-graphic svg {
            width: 280px;
            height: auto;
        }

        .bh-404-code {
            font-family: var(--font-heading);
            font-size: clamp(4rem, 10vw, 7rem);
            font-weight: 800;
            color: var(--color-primary);
            line-height: 1;
            margin-bottom: var(--space-2);
            letter-spacing: -0.04em;
        }

        .bh-404-title {
            font-family: var(--font-heading);
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-primary);
            margin-bottom: var(--space-3);
        }

        .bh-404-text {
            font-family: var(--font-body);
            font-size: 1.0625rem;
            line-height: 1.65;
            color: var(--color-text-muted);
            margin-bottom: var(--space-6);
        }

        .bh-404-search {
            max-width: 420px;
            margin: 0 auto var(--space-6);
        }

        .bh-404-search-box {
            display: flex;
            align-items: center;
            gap: var(--space-3);
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--border-radius);
            padding: 0 var(--space-4);
            height: 48px;
            transition: var(--transition-base);
        }

        .bh-404-search-box:focus-within {
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px var(--color-accent-subtle);
        }

        .bh-404-search-box i {
            color: var(--color-text-muted);
            font-size: 1rem;
            flex-shrink: 0;
        }

        .bh-404-search-input {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            font-family: var(--font-body);
            font-size: 1rem;
            color: var(--color-text);
            height: 100%;
        }

        .bh-404-search-input::placeholder {
            color: var(--color-text-light);
        }

        .bh-404-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--space-4);
        }

        .bh-404-home-link {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            font-family: var(--font-body);
            font-size: 1rem;
            font-weight: 600;
            color: var(--color-accent);
            text-decoration: none;
            transition: var(--transition-base);
        }

        .bh-404-home-link:hover {
            color: var(--color-accent-hover);
            text-decoration: underline;
        }

        .bh-404-categories {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: var(--space-2);
        }

        .bh-404-category-pill {
            display: inline-flex;
            align-items: center;
            gap: var(--space-2);
            padding: var(--space-2) var(--space-4);
            background-color: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--border-radius-pill);
            font-family: var(--font-body);
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--color-text);
            text-decoration: none;
            transition: var(--transition-base);
        }

        .bh-404-category-pill:hover {
            border-color: var(--color-accent);
            color: var(--color-accent);
            background-color: var(--color-surface-subtle);
        }

        .bh-404-label {
            font-family: var(--font-meta);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-text-muted);
            margin-bottom: var(--space-2);
        }
    </style>
</head>
<body>
    <div class="bh-404-page">
        <div class="bh-404-content">
            <!-- SVG Graphic -->
            <div class="bh-404-graphic">
                <svg viewBox="0 0 280 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <!-- Background circle -->
                    <circle cx="140" cy="100" r="80" fill="var(--color-surface-subtle)" />
                    <!-- Open book -->
                    <path d="M140 70 C120 60 95 58 70 62 L70 130 C95 126 120 128 140 138 C160 128 185 126 210 130 L210 62 C185 58 160 60 140 70 Z" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="2" />
                    <!-- Book spine -->
                    <path d="M140 70 L140 138" stroke="var(--color-border)" stroke-width="2" />
                    <!-- Text lines left -->
                    <path d="M80 75 L130 75" stroke="var(--color-border)" stroke-width="2" stroke-linecap="round" />
                    <path d="M80 85 L130 85" stroke="var(--color-border)" stroke-width="2" stroke-linecap="round" />
                    <path d="M80 95 L120 95" stroke="var(--color-border)" stroke-width="2" stroke-linecap="round" />
                    <!-- Text lines right -->
                    <path d="M150 75 L200 75" stroke="var(--color-border)" stroke-width="2" stroke-linecap="round" />
                    <path d="M150 85 L200 85" stroke="var(--color-border)" stroke-width="2" stroke-linecap="round" />
                    <path d="M150 95 L190 95" stroke="var(--color-border)" stroke-width="2" stroke-linecap="round" />
                    <!-- Question mark -->
                    <text x="140" y="165" text-anchor="middle" font-family="var(--font-heading)" font-size="32" font-weight="800" fill="var(--color-accent)">?</text>
                    <!-- Decorative dots -->
                    <circle cx="50" cy="50" r="4" fill="var(--color-accent)" opacity="0.3" />
                    <circle cx="230" cy="50" r="4" fill="var(--color-accent)" opacity="0.3" />
                    <circle cx="50" cy="150" r="4" fill="var(--color-accent)" opacity="0.3" />
                    <circle cx="230" cy="150" r="4" fill="var(--color-accent)" opacity="0.3" />
                </svg>
            </div>

            <div class="bh-404-code">404</div>
            <h1 class="bh-404-title">Page Not Found</h1>
            <p class="bh-404-text">
                The page you are looking for seems to have wandered off. It may have been moved, deleted, or perhaps it never existed at all.
            </p>

            <!-- Search Bar -->
            <div class="bh-404-search">
                <form action="/search-results" method="GET" class="bh-404-search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" name="q" class="bh-404-search-input" placeholder="Search articles..." aria-label="Search articles">
                </form>
            </div>

            <!-- Links -->
            <div class="bh-404-links">
                <a href="/" class="bh-404-home-link">
                    <i class="fa-solid fa-house"></i> Back to Home
                </a>
                <div>
                    <div class="bh-404-label">Popular Categories</div>
                    <div class="bh-404-categories">
                        <a href="/blogs?category=technology" class="bh-404-category-pill">
                            <i class="fa-solid fa-microchip"></i> Technology
                        </a>
                        <a href="/blogs?category=design" class="bh-404-category-pill">
                            <i class="fa-solid fa-pen-ruler"></i> Design
                        </a>
                        <a href="/blogs?category=culture" class="bh-404-category-pill">
                            <i class="fa-solid fa-palette"></i> Culture
                        </a>
                        <a href="/blogs?category=science" class="bh-404-category-pill">
                            <i class="fa-solid fa-flask"></i> Science
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="{{ asset('assets/js/data.js') }}" defer></script>
    <script src="{{ asset('assets/js/components.js') }}" defer></script>
    <script src="{{ asset('assets/js/main.js') }}" defer></script>
</body>
</html>
