<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error — BlogHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dark-mode.css') }}">
    <style>
        .error-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 0; }
        .error-content { text-align: center; max-width: 560px; padding: 0 1rem; }
        .error-graphic { margin-bottom: 1.5rem; }
        .error-graphic svg { width: 280px; height: auto; }
        .error-code { font-size: clamp(4rem, 10vw, 7rem); font-weight: 800; color: var(--color-primary); line-height: 1; margin-bottom: 0.5rem; letter-spacing: -0.04em; }
        .error-title { font-size: 1.5rem; font-weight: 700; color: var(--color-primary); margin-bottom: 0.75rem; }
        .error-text { font-size: 1.0625rem; line-height: 1.65; color: var(--color-text-muted); margin-bottom: 1.5rem; }
        .error-search { max-width: 420px; margin: 0 auto 1.5rem; }
        .error-links { display: flex; flex-direction: column; align-items: center; gap: 1rem; }
        .error-home-link { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 1rem; font-weight: 600; color: var(--color-accent); text-decoration: none; }
        .error-home-link:hover { text-decoration: underline; }
        .error-categories { display: flex; flex-wrap: wrap; justify-content: center; gap: 0.5rem; }
        .error-category-pill { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background-color: var(--color-surface); border: 1px solid var(--color-border); border-radius: 9999px; font-size: 0.875rem; font-weight: 600; color: var(--color-text); text-decoration: none; }
        .error-category-pill:hover { border-color: var(--color-accent); color: var(--color-accent); }
        .error-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-text-muted); margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-content">
            <div class="error-graphic">
                <svg viewBox="0 0 280 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="140" cy="100" r="80" fill="var(--color-surface-subtle)" />
                    <rect x="80" y="60" width="120" height="90" rx="8" fill="var(--color-surface)" stroke="var(--color-border)" stroke-width="2" />
                    <circle cx="140" cy="90" r="20" fill="none" stroke="var(--color-warning)" stroke-width="3" />
                    <line x1="140" y1="80" x2="140" y2="95" stroke="var(--color-warning)" stroke-width="3" stroke-linecap="round" />
                    <circle cx="140" cy="102" r="2" fill="var(--color-warning)" />
                    <rect x="100" y="125" width="80" height="8" rx="4" fill="var(--color-border)" />
                    <circle cx="50" cy="50" r="4" fill="var(--color-accent)" opacity="0.3" />
                    <circle cx="230" cy="50" r="4" fill="var(--color-accent)" opacity="0.3" />
                    <circle cx="50" cy="150" r="4" fill="var(--color-accent)" opacity="0.3" />
                    <circle cx="230" cy="150" r="4" fill="var(--color-accent)" opacity="0.3" />
                </svg>
            </div>
            <div class="error-code">500</div>
            <h1 class="error-title">Something Went Wrong</h1>
            <p class="error-text">We are experiencing technical difficulties. Please try again in a few moments. If the problem persists, contact our support team.</p>
            <div class="error-search">
                <form action="/search" method="GET" class="d-flex gap-2">
                    <input type="search" name="q" class="form-control" placeholder="Search articles..." aria-label="Search articles">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i></button>
                </form>
            </div>
            <div class="error-links">
                <a href="/" class="error-home-link"><i class="fa-solid fa-house"></i> Back to Home</a>
                <div>
                    <div class="error-label">Popular Categories</div>
                    <div class="error-categories">
                        <a href="/blogs?category=technology" class="error-category-pill"><i class="fa-solid fa-microchip"></i> Technology</a>
                        <a href="/blogs?category=design" class="error-category-pill"><i class="fa-solid fa-pen-ruler"></i> Design</a>
                        <a href="/blogs?category=culture" class="error-category-pill"><i class="fa-solid fa-palette"></i> Culture</a>
                        <a href="/blogs?category=science" class="error-category-pill"><i class="fa-solid fa-flask"></i> Science</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
