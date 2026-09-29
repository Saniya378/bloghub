# BlogHub — Modern Publishing Platform

> A full-stack editorial platform featuring long-form essays, tech deep dives, and cultural commentary — built with Laravel 12, Bootstrap 5, and vanilla JavaScript.

---

## Table of Contents

- [Description](#description)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Folder Structure](#folder-structure)
- [Brand Identity](#brand-identity)
- [Entity Relationship Diagram](#entity-relationship-diagram)
- [Demo Accounts](#demo-accounts)
- [Setup Instructions](#setup-instructions)
- [Testing](#testing)
- [Screenshots](#screenshots)
- [License](#license)

---

## Description

BlogHub is a curated publishing platform where independent writers, systems engineers, and creative theorists publish in-depth essays, architectural breakdowns, and unhurried reflections. The platform emphasizes editorial quality, reader respect, and sustainable monetization over engagement-optimized content farms.

The application demonstrates a complete Laravel 12 architecture with server-side rendering via Blade templates, a client-side data layer for dynamic content, comprehensive authentication with OTP-based password reset, article CRUD with authorization, comment moderation, and a full filtering/search/pagination system.

---

## Features

### Core Features
- **Article Publishing** — Create, edit, delete articles with rich HTML content, tags, categories, and draft/published workflow
- **User Authentication** — Registration, login, logout with rate limiting, session management, and inactive account blocking
- **OTP Password Reset** — Email-based one-time password flow with expiration and single-use enforcement
- **Comment System** — Submit comments with moderation workflow (pending → approved/rejected)
- **Social Interactions** — Like articles, bookmark for later, follow authors
- **Search & Filtering** — Full-text search, category/author filtering, sorting (newest/oldest/popular), pagination
- **Responsive Design** — Mobile-first layout with Bootstrap 5 grid system
- **Dark Mode** — Theme toggle with localStorage persistence
- **PWA Support** — Service worker for offline caching, manifest for installability

### Security Features
- CSRF protection on all forms
- XSS sanitization on article content (allowlist-based)
- SQL injection prevention via Eloquent ORM
- Rate limiting on authentication endpoints
- Security headers (CSP, X-Frame-Options, X-Content-Type-Options)
- Mass assignment protection via `$fillable`
- Authorization checks on all mutating operations
- Account activation/deactivation support

### Performance Features
- Lazy loading images with `loading="lazy"` and `decoding="async"`
- Width/height attributes to prevent layout shift (CLS)
- Deferred script loading
- Preconnect/dns-prefetch hints for CDN origins
- Critical CSS preloading
- HTTP cache-control headers
- Service worker for static asset caching

---

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Backend Framework** | Laravel | ^12.0 |
| **PHP** | PHP | ^8.2 |
| **Database** | SQLite (default), MySQL/PostgreSQL | — |
| **ORM** | Eloquent | — |
| **Frontend Templating** | Blade | — |
| **CSS Framework** | Bootstrap | 5.3.3 |
| **Icons** | Font Awesome | 6.5.1 |
| **Build Tool** | Vite | ^7.0.7 |
| **Testing** | PHPUnit | ^11.5.50 |
| **Package Manager** | Composer + npm | — |

---

## Folder Structure

```
Bloghub/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       # Login, register, logout
│   │   │   ├── ArticleController.php    # Article CRUD with authorization
│   │   │   ├── CommentController.php    # Comment CRUD with moderation
│   │   │   ├── OtpController.php        # OTP request, verify, reset
│   │   │   └── ToggleController.php     # Like, bookmark, follow toggles
│   │   └── Middleware/
│   │       ├── Authenticate.php         # Auth + active status check
│   │       ├── RateLimit.php            # Generic rate limiting
│   │       ├── SecurityHeaders.php      # CSP, X-Frame-Options, etc.
│   │       └── VerifyCsrfToken.php      # CSRF token validation
│   ├── Models/
│   │   ├── User.php                     # User with roles, is_active
│   │   ├── Article.php                  # Article with draft/published
│   │   └── Comment.php                  # Comment with moderation status
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Support/
│       └── HtmlSanitizer.php            # Server-side HTML sanitizer
├── bootstrap/
│   ├── app.php                          # Middleware registration
│   └── providers.php
├── config/
│   ├── app.php                          # APP_DEBUG, APP_ENV
│   ├── cache.php
│   ├── database.php
│   └── session.php
├── database/
│   ├── database.sqlite
│   ├── factories/
│   │   ├── UserFactory.php
│   │   ├── ArticleFactory.php
│   │   └── CommentFactory.php
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_09_29_000000_add_security_fields_to_users_table.php
│   │   ├── 2026_09_29_000001_create_articles_table.php
│   │   ├── 2026_09_29_000002_create_comments_table.php
│   │   └── 2026_09_29_000003_create_interactions_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── public/
│   ├── index.php
│   ├── manifest.json                    # PWA manifest
│   ├── sw.js                            # Service worker
│   └── assets/
│       ├── css/
│       │   ├── style.css                # Design tokens, typography, utilities
│       │   ├── components.css           # Component styles
│       │   └── dark-mode.css            # Dark theme overrides
│       └── js/
│           ├── data.js                  # Centralized content data
│           ├── components.js            # Reusable render functions
│           └── main.js                  # Theme, scroll, search, toasts
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php            # Main layout with preconnect, defer
│       ├── partials/
│       │   ├── navbar.blade.php         # Auth-aware navigation
│       │   ├── footer.blade.php
│       │   └── search-overlay.blade.php
│       ├── pages/
│       │   ├── home.blade.php
│       │   ├── blogs.blade.php
│       │   ├── blog-details.blade.php
│       │   ├── categories.blade.php
│       │   ├── authors.blade.php
│       │   ├── author-profile.blade.php
│       │   ├── search-results.blade.php
│       │   ├── about.blade.php
│       │   ├── contact.blade.php
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   ├── forgot-password.blade.php
│       │   ├── verify-otp.blade.php
│       │   ├── reset-password.blade.php
│       │   ├── privacy.blade.php
│       │   ├── terms.blade.php
│       │   └── ui-kit.blade.php
│       └── errors/
│           └── 404.blade.php
├── routes/
│   ├── web.php                          # All routes with middleware
│   └── console.php
├── tests/
│   ├── Feature/
│   │   ├── AuthTest.php
│   │   ├── OtpFlowTest.php
│   │   ├── PostAuthorizationTest.php
│   │   ├── ToggleTest.php
│   │   ├── CommentTest.php
│   │   └── BlogFilteringTest.php
│   └── Unit/
│       └── ExampleTest.php
├── .env.example                         # Safe environment template
├── composer.json
├── package.json
├── phpunit.xml
├── vite.config.js
└── artisan
```

---

## Brand Identity

### Color Palette

| Token | Light Mode | Dark Mode | Usage |
|-------|-----------|-----------|-------|
| `--color-primary` | `#1F2A44` | `#9BB7E7` | Headings, brand elements |
| `--color-accent` | `#C8461F` | `#E85B33` | CTAs, highlights, active states |
| `--color-secondary` | `#3F5A45` | `#6EA378` | Success states, secondary actions |
| `--color-bg` | `#FBFAF7` | `#0D1117` | Page background |
| `--color-surface` | `#FFFFFF` | `#161D2A` | Cards, inputs, surfaces |
| `--color-text` | `#14171F` | `#F1F4FA` | Body text |
| `--color-text-muted` | `#575E6C` | `#9DA7B8` | Secondary text |
| `--color-border` | `#D8D5CC` | `#2B374D` | Dividers, borders |

### Typography

| Role | Font Family | Usage |
|------|------------|-------|
| **Headings** | Fraunces (serif) | H1–H3, brand name, section titles |
| **Body** | Inter (sans-serif) | Paragraphs, descriptions, UI text |
| **Meta/Labels** | IBM Plex Mono (monospace) | Captions, dates, tags, stats |

### Type Scale

| Element | Size | Line Height | Weight |
|---------|------|-------------|--------|
| H1 | 2.5rem (40px) | 1.25 | 700 |
| H2 | 1.75rem (28px) | 1.3 | 700 |
| H3 | 1.25rem (20px) | 1.35 | 600 |
| Body | 1rem (16px) | 1.6 | 400 |
| Caption | 0.8125rem (13px) | 1.4 | 400 |

### Spacing Scale (8px Grid)

| Token | Value | Usage |
|-------|-------|-------|
| `--space-1` | 4px | Tight gaps, icon spacing |
| `--space-2` | 8px | Small gaps, inline elements |
| `--space-3` | 12px | Medium gaps, list items |
| `--space-4` | 16px | Default padding, card gaps |
| `--space-5` | 20px | Section padding |
| `--space-6` | 24px | Large section gaps |
| `--space-8` | 32px | Hero sections |
| `--space-12` | 48px | Page sections |

### Border Radius

| Token | Value | Usage |
|-------|-------|-------|
| `--border-radius-sm` | 4px | Small elements, inputs |
| `--border-radius` | 8px | Cards, buttons, default |
| `--border-radius-pill` | 9999px | Pills, avatars, tags |

---

## Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              BlogHub ERD                                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐         ┌──────────────┐         ┌──────────────┐        │
│  │    users     │         │   articles   │         │   comments   │        │
│  ├──────────────┤         ├──────────────┤         ├──────────────┤        │
│  │ id (PK)      │◄────────┤ id (PK)      │◄────────┤ id (PK)      │        │
│  │ name         │    1:M  │ user_id (FK) │    1:M  │ user_id (FK) │        │
│  │ email (UQ)   │         │ title        │         │ article_id   │        │
│  │ password     │         │ slug (UQ)    │         │ content      │        │
│  │ role         │         │ excerpt      │         │ status       │        │
│  │ is_active    │         │ content      │         │ likes        │        │
│  │ email_verified│        │ category_slug│         │ created_at   │        │
│  │ remember_token│         │ tags (JSON)  │         │ updated_at   │        │
│  │ created_at   │         │ status       │         └──────────────┘        │
│  │ updated_at   │         │ views        │                                   │
│  └──────────────┘         │ likes        │         ┌──────────────┐        │
│                           │ comment_count│         │ article_likes│        │
│                           │ reading_time │         ├──────────────┤        │
│                           │ published_at │         │ id (PK)      │        │
│                           │ created_at   │         │ user_id (FK) │        │
│                           │ updated_at   │         │ article_id   │        │
│                           └──────────────┘         │ created_at   │        │
│                                                    └──────────────┘        │
│  ┌──────────────┐         ┌──────────────┐         ┌──────────────┐        │
│  │  bookmarks   │         │   follows    │         │ comment_likes│        │
│  ├──────────────┤         ├──────────────┤         ├──────────────┤        │
│  │ id (PK)      │         │ id (PK)      │         │ id (PK)      │        │
│  │ user_id (FK) │         │ user_id (FK) │         │ user_id (FK) │        │
│  │ article_id   │         │ author_id(FK)│         │ comment_id   │        │
│  │ created_at   │         │ created_at   │         │ created_at   │        │
│  └──────────────┘         └──────────────┘         └──────────────┘        │
│                                                                             │
│  ┌──────────────┐         ┌──────────────┐         ┌──────────────┐        │
│  │   sessions   │         │password_reset│         │    cache     │        │
│  ├──────────────┤         ├──────────────┤         ├──────────────┤        │
│  │ id (PK)      │         │ email (PK)   │         │ key (PK)     │        │
│  │ user_id (FK) │         │ token        │         │ value        │        │
│  │ ip_address   │         │ created_at   │         │ expiration   │        │
│  │ user_agent   │         └──────────────┘         └──────────────┘        │
│  │ payload      │                                                          │
│  │ last_activity│                                                          │
│  └──────────────┘                                                          │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘

Relationships:
  users 1:M articles      (A user writes many articles)
  users 1:M comments      (A user writes many comments)
  articles 1:M comments   (An article has many comments)
  users M:M articles      (article_likes - users like many articles)
  users M:M articles      (bookmarks - users bookmark many articles)
  users M:M users         (follows - users follow many authors)
  users M:M comments      (comment_likes - users like many comments)
```

---

## Demo Accounts

| Role | Email | Password | Description |
|------|-------|----------|-------------|
| **User** | `test@example.com` | `password` | Default seeded user for testing |
| **Admin** | `admin@bloghub.com` | `AdminPass1` | Full access to moderation |

> **Note:** Run `php artisan db:seed` to create the default test user. Create an admin account via tinker:
> ```bash
> php artisan tinker
> >>> \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@bloghub.com', 'password' => 'AdminPass1', 'role' => 'admin']);
> ```

---

## Setup Instructions

### Prerequisites

- PHP ^8.2
- Composer ^2.0
- Node.js ^18.0
- npm ^9.0

### Step-by-Step Setup

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/bloghub.git
cd bloghub

# 2. Install PHP dependencies
composer install

# 3. Create environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Create SQLite database (if not exists)
touch database/database.sqlite

# 6. Run migrations and seeders
php artisan migrate --seed

# 7. Create storage link
php artisan storage:link

# 8. Install Node dependencies
npm install

# 9. Build frontend assets
npm run build

# 10. Start development server
php artisan serve
```

### Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_NAME` | `BlogHub` | Application name |
| `APP_ENV` | `production` | Environment (local/staging/production) |
| `APP_KEY` | — | Encryption key (auto-generated) |
| `APP_DEBUG` | `false` | Debug mode (set false in production) |
| `APP_URL` | `http://localhost` | Application URL |
| `DB_CONNECTION` | `sqlite` | Database connection |
| `DB_DATABASE` | `database/database.sqlite` | SQLite database path |
| `CACHE_STORE` | `database` | Cache driver |
| `SESSION_DRIVER` | `database` | Session driver |
| `QUEUE_CONNECTION` | `database` | Queue driver |
| `MAIL_MAILER` | `log` | Mail driver |

---

## Testing

```bash
# Run all tests
php artisan test

# Run feature tests only
php artisan test --testsuite=Feature

# Run unit tests only
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Test Coverage

| Test File | Coverage Area |
|-----------|--------------|
| `AuthTest.php` | Registration, login, logout, validation, rate limiting |
| `OtpFlowTest.php` | OTP request, verification, password reset, expiration |
| `PostAuthorizationTest.php` | Article CRUD, ownership, admin override, mass assignment |
| `ToggleTest.php` | Like, bookmark, follow toggles, idempotency |
| `CommentTest.php` | Submission, moderation, approval/rejection, deletion |
| `BlogFilteringTest.php` | Search, filter, sort, pagination, SQL injection prevention |

---

## Screenshots

### Home Page
![Home Page](screenshots/home.png)
*Hero section, featured articles, trending posts, categories, authors*

### Blogs Listing
![Blogs](screenshots/blogs.png)
*Article grid with search, filters, and pagination*

### Blog Details
![Blog Details](screenshots/blog-details.png)
*Full article view with author box, comments, related posts*

### Author Profile
![Author Profile](screenshots/author-profile.png)
*Cover image, bio, stats, article grid*

### Categories
![Categories](screenshots/categories.png)
*Category grid with icons and article counts*

### Dark Mode
![Dark Mode](screenshots/dark-mode.png)
*Full dark theme with inverted colors*

### Mobile Responsive
![Mobile](screenshots/mobile.png)
*Mobile navigation, responsive grid, touch targets*

---

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Credits

- **Design System** — Custom CSS with 8px grid, Fraunces/Inter/IBM Plex Mono typography
- **Icons** — Font Awesome 6.5.1
- **Images** — Picsum Photos, Pravatar
- **Framework** — Laravel 12
