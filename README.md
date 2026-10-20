# BlogHub — Multi-Role Modern Publishing & Editorial Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Sanctum](https://img.shields.io/badge/Laravel-Sanctum_v4-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![Vite](https://img.shields.io/badge/Vite-7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![PWA Ready](https://img.shields.io/badge/PWA-Enabled-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)](#pwa--offline-support)
[![Tests Passing](https://img.shields.io/badge/Tests-17_Suites_Passing-brightgreen?style=for-the-badge&logo=phpunit&logoColor=white)](#testing--quality-assurance)

> A full-stack, enterprise-grade publishing ecosystem featuring long-form journalism, technical deep dives, hierarchical discussion threads, role-based workflows, comprehensive analytics, and a RESTful API for multi-platform delivery. Built with **Laravel 12**, **Bootstrap 5**, **Laravel Sanctum**, and modern vanilla JavaScript.

---

## Table of Contents

- [Overview & Vision](#overview--vision)
- [System Architecture & Roles](#system-architecture--roles)
  - [Role-Based Access Control (RBAC)](#role-based-access-control-rbac)
  - [Roles & Permissions Matrix](#roles--permissions-matrix)
- [Core Features & Modules](#core-features--modules)
  - [1. Editorial Review & Article Lifecycle](#1-editorial-review--article-lifecycle)
  - [2. Threaded & Moderated Comments](#2-threaded--moderated-comments)
  - [3. Social Engagements & Live Feed](#3-social-engagements--live-feed)
  - [4. Reader Favorites & Library Hub](#4-reader-favorites--library-hub)
  - [5. Real-Time Notification Center](#5-real-time-notification-center)
  - [6. Author Dashboard (`/dashboard`)](#6-author-dashboard-dashboard)
  - [7. Admin Control Center (`/admin`)](#7-admin-control-center-admin)
  - [8. Advanced Analytics & Reports Engine (`/admin/reports`)](#8-advanced-analytics--reports-engine-adminreports)
  - [9. Contact Inbox & Newsletter Engine](#9-contact-inbox--newsletter-engine)
  - [10. PWA & Offline Support](#10-pwa--offline-support)
- [RESTful API Reference (`/api/v1`)](#restful-api-reference-apiv1)
  - [Authentication & Headers](#authentication--headers)
  - [API Endpoints Directory](#api-endpoints-directory)
- [Database Architecture & ERD](#database-architecture--erd)
- [Design System & UI Tokens](#design-system--ui-tokens)
- [Folder Structure](#folder-structure)
- [Demo Credentials](#demo-credentials)
- [Installation & Local Setup](#installation--local-setup)
- [Environment Configuration](#environment-configuration)
- [Testing & Quality Assurance](#testing--quality-assurance)
- [Security Architecture](#security-architecture)
- [License & Credits](#license--credits)

---

## Overview & Vision

**BlogHub** is an opinionated, high-performance publishing platform crafted for independent essayists, technology researchers, and cultural journalists. In contrast to engagement-maximizing clickbait feeds, BlogHub prioritizes **editorial integrity, reading typography, nuanced discussion threads, and transparent governance**.

The application operates as both a **server-rendered web portal (Blade + Vite)** and a **headless RESTful backend (Laravel Sanctum)** capable of powering mobile apps and single-page applications.

---

## System Architecture & Roles

BlogHub enforces strict multi-tenancy and authorization guards across all HTTP endpoints and controllers.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        BlogHub Role Hierarchy                          │
├────────────────────────────────────────────────────────────────────────┤
│                                                                        │
│  [ Guest / Visitor ]                                                   │
│    └─ Read published articles, browse categories & authors            │
│    └─ Submit contact inquiries & subscribe to newsletter               │
│                                                                        │
│  [ Reader / Member ] (Role: `reader`)                                  │
│    ├─ Inherits all Guest capabilities                                  │
│    ├─ Like articles & bookmark posts into personal library             │
│    ├─ Post threaded comments & like comments                           │
│    ├─ Follow authors & receive custom personalized feed                │
│    ├─ Manage Favorites Hub (`/favorites`) & profile settings          │
│    └─ In-app notifications & Apply to become an Author                 │
│                                                                        │
│  [ Author / Contributor ] (Role: `author`)                             │
│    ├─ Inherits all Reader capabilities                                 │
│    ├─ Dedicated Author Dashboard (`/dashboard`)                        │
│    ├─ Create, draft, edit, and submit articles for editorial review    │
│    ├─ Real-time article readership statistics (views, likes, comments) │
│    └─ Moderate reader comments on their own published articles         │
│                                                                        │
│  [ Super Administrator ] (Role: `admin`)                               │
│    ├─ Full system governance at (`/admin`)                             │
│    ├─ Approve / reject / feature / unpublish any article               │
│    ├─ Moderate users (activate/deactivate, promote/demote roles)       │
│    ├─ Manage categories, testimonials, and contact inbox               │
│    ├─ Export newsletter subscribers to CSV                             │
│    └─ Comprehensive Analytics & Reports Engine (`/admin/reports`)      │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

### Roles & Permissions Matrix

| Capability / Feature | Guest | Reader | Author | Admin |
|:---|:---:|:---:|:---:|:---:|
| Read Published Articles | ✅ | ✅ | ✅ | ✅ |
| Filter, Sort & Search Posts | ✅ | ✅ | ✅ | ✅ |
| View Author Profiles | ✅ | ✅ | ✅ | ✅ |
| Like Articles & Bookmarks | ❌ | ✅ | ✅ | ✅ |
| Post Threaded Comments | ❌ | ✅ | ✅ | ✅ |
| Like Comments | ❌ | ✅ | ✅ | ✅ |
| Follow Authors | ❌ | ✅ | ✅ | ✅ |
| Access Favorites Hub (`/favorites`) | ❌ | ✅ | ✅ | ✅ |
| In-App Notification Center | ❌ | ✅ | ✅ | ✅ |
| Request Author Status | ❌ | ✅ | — | — |
| Access Author Dashboard (`/dashboard`) | ❌ | ❌ | ✅ | 🔄 *(Redirects)* |
| Write / Edit / Delete Own Articles | ❌ | ❌ | ✅ | ✅ |
| Moderate Comments on Own Articles | ❌ | ❌ | ✅ | ✅ |
| Access Admin Panel (`/admin`) | ❌ | ❌ | ❌ | ✅ |
| Approve / Reject Article Submissions | ❌ | ❌ | ❌ | ✅ |
| Toggle User Roles & Active Status | ❌ | ❌ | ❌ | ✅ |
| Category & Testimonial Management | ❌ | ❌ | ❌ | ✅ |
| View Activity Logs & Audit Trail | ❌ | ❌ | ❌ | ✅ |
| Export Analytics & CSV Reports | ❌ | ❌ | ❌ | ✅ |

---

## Core Features & Modules

### 1. Editorial Review & Article Lifecycle

Articles in BlogHub move through a robust state machine ensuring editorial quality:

```
 [ Author Draft ] ──▶ [ Submitted / Pending Review ] ──▶ [ Admin Review ]
                                                              │
                                            ┌─────────────────┴─────────────────┐
                                            ▼                                   ▼
                                      [ Approved ]                        [ Rejected ]
                                            │                         (With feedback reason)
                                            ▼
                                   [ Published Live ]
                                            │
                                            ├──▶ [ Featured Flag Toggle ]
                                            └──▶ [ Admin Unpublish / Archive ]
```

- **Rich Content & SEO**: Articles support HTML markup with sanitization, custom excerpts, cover banners, tags (stored as JSON), and reading time estimation.
- **Slug Management**: URL-safe unique slugs generated automatically from titles.
- **View Counting**: Atomic, race-condition protected view increments on visits.

### 2. Threaded & Moderated Comments

- **Recursive Hierarchies**: Supports multi-level parent-child replies via `parent_id`.
- **Live Thread Updates**: Dedicated `/comments/{articleId}/thread` fragment endpoint allowing dynamic DOM updates without full-page reloads.
- **Dual-Layer Moderation**:
  - Authors can moderate comments specifically on their own articles via `/dashboard/comments`.
  - Admins retain overarching power to approve or permanently delete comments platform-wide.
- **Community Likes**: Users can like and react to individual comments.

### 3. Social Engagements & Live Feed

- **Follow Engine**: Readers can follow specific authors. Toggling a follow dynamically impacts the user's live feed stream.
- **Interactive Toggles**: Immediate AJAX toggles for likes, bookmarks, and follows with optimistic UI state transitions and CSRF handling.
- **Live Relative Timestamps**: Client-side updater converts UTC timestamps into human-readable relative formats (*"3 minutes ago"*, *"just now"*).

### 4. Reader Favorites & Library Hub

Dedicated destination (`/favorites`) allowing members to organize their reading life:
- **Bookmarked Articles**: Saved reads with direct un-bookmark toggles.
- **Liked Articles**: Quick reference to appreciated essays.
- **Followed Authors**: Directory of subscribed creators with instant unfollow actions.
- **Liked Comments**: History of insightful reader contributions.

### 5. Real-Time Notification Center

- Notification bell in navbar with live unread indicator badge.
- Events generating notifications:
  - Article approval/rejection updates to authors.
  - New replies to user comments.
  - New followers.
  - New comments on authored articles.
- Actions: Mark single notification as read, mark all as read.

### 6. Author Dashboard (`/dashboard`)

A distraction-free workbench dedicated to creators:
- **Performance KPI Cards**: Total published articles, aggregate reader views, accumulated likes, and active comments.
- **Post Management Grid**: Filter by status (*Draft*, *Pending*, *Approved*, *Rejected*, *Published*).
- **Article Editor**: Title, category assignment, tag picker, excerpt summary, cover image URL, and rich content workspace.
- **Comment Moderation Desk**: Review recent reader discussions on authored posts with inline approve/delete buttons.

### 7. Admin Control Center (`/admin`)

Unified command portal protected by `auth` and `role:admin` middleware:
- **Overview Dashboard**: High-level platform KPIs, recent registrations, pending review queues, and quick actions.
- **User Management**: Search user database, filter by role, activate/deactivate suspect accounts, and alter user roles (`reader` ↔ `author` ↔ `admin`).
- **Post Moderation Queue**: Review incoming author submissions, inspect full content, one-click approve, reject with notes, toggle featured banner placement, or unpublish.
- **Category Taxonomy Manager**: Create, edit, and delete categories with custom icons and slugs.
- **Testimonials Hub**: Curate platform testimonials displayed on public landings.
- **Contact Message Inbox**: Read reader inquiries, mark messages as resolved/read, and prune old inquiries.
- **Subscriber Directory**: Inspect newsletter subscribers with one-click CSV export.

### 8. Advanced Analytics & Reports Engine (`/admin/reports`)

An enterprise reporting suite providing granular visibility into platform health, with instant CSV/Excel export pipelines:

1. **Reports Dashboard (`/admin/reports`)**: Consolidated performance summary and aggregate charts.
2. **Author Reports (`/admin/reports/authors`)**: Author onboarding velocity, active contributors, top authors by readership, and registration logs.
3. **Article Performance (`/admin/reports/articles`)**: Publishing breakdown by category, most viewed articles, engagement ratios, and full article database export.
4. **Approval Workflow Audit (`/admin/reports/approvals`)**: Author application backlog, article review turnaround times, and approval/rejection ratios.
5. **Engagement & Traffic (`/admin/reports/engagement`)**: Reader view metrics, like velocity, and comment engagement curves.
6. **Platform Activity Log (`/admin/reports/activity`)**: Immutable audit trail recording user logins, role updates, post submissions, and administrative overrides.

### 9. Contact Inbox & Newsletter Engine

- Rate-limited contact inquiry form (`throttle:5,1`) with validation and database persistence.
- Newsletter subscription endpoint with email deduplication, regex validation, and rate protection.

### 10. PWA & Offline Support

- **Web App Manifest** (`manifest.json`): Configured with theme colors, icons, and standalone display mode for home screen installation on Android and iOS.
- **Service Worker** (`sw.js`): Intelligent cache-first strategy for static assets and offline fallback caching.

---

## RESTful API Reference (`/api/v1`)

BlogHub exposes a complete REST API versioned under `/api/v1` powered by **Laravel Sanctum**.

### Authentication & Headers

Protected routes require the `Authorization` header containing a valid Bearer token obtained from the login/register endpoints:

```http
Authorization: Bearer <sanctum_personal_access_token>
Accept: application/json
Content-Type: application/json
```

### Standard Response Envelope

```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": { ... }
}
```

### API Endpoints Directory

#### 🔐 Authentication & Password Recovery

| Method | Endpoint | Description | Auth Required |
|:---|:---|:---|:---:|
| `POST` | `/api/v1/auth/register` | Register new reader account and receive Sanctum token | No |
| `POST` | `/api/v1/auth/login` | Authenticate with email/password; returns user + token | No |
| `POST` | `/api/v1/auth/forgot-password` | Send 6-digit OTP code to registered email | No |
| `POST` | `/api/v1/auth/verify-otp` | Verify validity of 6-digit OTP code | No |
| `POST` | `/api/v1/auth/reset-password` | Set new password using verified OTP token | No |
| `GET` | `/api/v1/auth/me` | Fetch authenticated user profile and permissions | Bearer Token |
| `POST` | `/api/v1/auth/logout` | Revoke current access token and invalidate session | Bearer Token |

#### 📰 Articles & Content

| Method | Endpoint | Description | Auth Required |
|:---|:---|:---|:---:|
| `GET` | `/api/v1/articles` | List published articles (supports `search`, `category`, `sort`, `page`) | No |
| `GET` | `/api/v1/articles/feed` | Personalized reading feed based on followed authors | Bearer Token / Optional |
| `GET` | `/api/v1/articles/highlights` | Fetch featured and trending spotlight articles | No |
| `GET` | `/api/v1/articles/{id_or_slug}` | Retrieve single article details, author card, and stats | No |
| `POST` | `/api/v1/articles` | Create a new article (author/admin only) | Bearer Token |
| `PUT` | `/api/v1/articles/{id}` | Update article details or submit for review | Bearer Token |
| `DELETE`| `/api/v1/articles/{id}` | Permanently delete an article | Bearer Token |
| `POST` | `/api/v1/articles/{id}/toggle-like` | Like or unlike an article | Bearer Token |
| `POST` | `/api/v1/articles/{id}/toggle-bookmark` | Add or remove article from personal bookmarks | Bearer Token |

#### 💬 Comments & Discussions

| Method | Endpoint | Description | Auth Required |
|:---|:---|:---|:---:|
| `GET` | `/api/v1/articles/{articleId}/comments` | Retrieve nested comments thread for an article | No |
| `POST` | `/api/v1/articles/{articleId}/comments` | Submit new comment or reply (supports `parent_id`) | Bearer Token |
| `DELETE`| `/api/v1/comments/{id}` | Delete own comment (or author/admin moderation) | Bearer Token |
| `POST` | `/api/v1/comments/{id}/toggle-like` | Like or unlike a specific comment | Bearer Token |

#### ✍️ Authors & Creator Hub

| Method | Endpoint | Description | Auth Required |
|:---|:---|:---|:---:|
| `GET` | `/api/v1/authors` | List all verified platform authors | No |
| `GET` | `/api/v1/authors/featured` | Fetch top-performing spotlight authors | No |
| `GET` | `/api/v1/authors/{id_or_username}` | Fetch author profile, bio, specialties, and socials | No |
| `GET` | `/api/v1/authors/{id}/articles` | List articles authored by specific user | No |
| `POST` | `/api/v1/authors/{id}/toggle-follow`| Follow or unfollow an author | Bearer Token |
| `GET` | `/api/v1/authors/{id}/follow-status`| Check if authenticated user follows this author | Bearer Token |
| `GET` | `/api/v1/author/dashboard` | Author statistics: total views, likes, articles, comments | Bearer Token |
| `GET` | `/api/v1/author/comments` | Retrieve pending/approved comments on author's posts | Bearer Token |
| `POST` | `/api/v1/author/comments/{id}/approve` | Author approves comment on their post | Bearer Token |
| `DELETE`| `/api/v1/author/comments/{id}` | Author rejects/deletes comment on their post | Bearer Token |

#### 📂 Categories & Search

| Method | Endpoint | Description | Auth Required |
|:---|:---|:---|:---:|
| `GET` | `/api/v1/categories` | List all available categories with article counts | No |
| `GET` | `/api/v1/categories/{slug}` | Get category metadata and matching articles | No |
| `GET` | `/api/v1/search` | Search articles by keyword, category, tag, or author | No |
| `GET` | `/api/v1/search/suggestions` | Instant search autocomplete suggestions | No |

#### 👤 Profile & Notifications

| Method | Endpoint | Description | Auth Required |
|:---|:---|:---|:---:|
| `GET` | `/api/v1/profile` | Fetch profile, stats, followed authors, bookmarks | Bearer Token |
| `POST` | `/api/v1/profile/update` | Update name, username, bio, tagline, socials, avatar | Bearer Token |
| `POST` | `/api/v1/profile/password` | Update account password | Bearer Token |
| `GET` | `/api/v1/profile/bookmarks` | Fetch user's saved reading list | Bearer Token |
| `GET` | `/api/v1/profile/favorites` | Fetch liked posts, liked comments, and bookmarks | Bearer Token |
| `POST` | `/api/v1/profile/become-author` | Submit request to elevate account to Author | Bearer Token |
| `GET` | `/api/v1/notifications` | Fetch unread and recent in-app notifications | Bearer Token |
| `POST` | `/api/v1/notifications/{id}/read`| Mark single notification as read | Bearer Token |
| `POST` | `/api/v1/notifications/read-all` | Mark all unread notifications as read | Bearer Token |

#### 🌐 Platform & Public Meta

| Method | Endpoint | Description | Auth Required |
|:---|:---|:---|:---:|
| `GET` | `/api/v1/stats` | Platform statistics (total posts, authors, readers) | No |
| `GET` | `/api/v1/testimonials` | Public reader testimonials | No |
| `POST` | `/api/v1/contact` | Submit contact inquiry | No |
| `POST` | `/api/v1/newsletter/subscribe` | Join email newsletter dispatch | No |

---

## Database Architecture & ERD

BlogHub utilizes a relational schema optimized for editorial workflows, interaction counts, and audit trails:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                                 BlogHub Relational ERD                                 │
├────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                        │
│  ┌──────────────────────┐           ┌──────────────────────┐                           │
│  │        users         │           │      categories      │                           │
│  ├──────────────────────┤           ├──────────────────────┤                           │
│  │ id (PK)              │           │ id (PK)              │                           │
│  │ name                 │           │ name                 │                           │
│  │ username (UQ)        │           │ slug (UQ)            │                           │
│  │ email (UQ)           │           │ description          │                           │
│  │ password             │           │ icon                 │                           │
│  │ role (reader/author/ │           │ created_at           │                           │
│  │       admin)         │           └──────────┬───────────┘                           │
│  │ is_active (bool)     │                      │ 1:M                                   │
│  │ avatar, cover_image  │                      │                                       │
│  │ bio, tagline         │                      ▼                                       │
│  │ specialty            │           ┌──────────────────────┐   1:M  ┌────────────────┐ │
│  │ social (JSON)        │ 1:M       │       articles       ├───────►│    comments    │ │
│  │ email_verified_at    ├──────────►│ id (PK)              │        ├────────────────┤ │
│  │ remember_token       │           │ user_id (FK -> users)│        │ id (PK)        │ │
│  │ created_at           │           │ category_id / slug   │        │ user_id (FK)   │ │
│  └──────────┬───────────┘           │ title, slug (UQ)     │        │ article_id (FK)│ │
│             │                       │ excerpt, content     │        │ parent_id (FK) ◄─┐ (Replies)
│             │                       │ cover_image          │        │ content        │ └──┘
│             │                       │ tags (JSON)          │        │ status         │ │
│             │                       │ status (draft/       │        │ likes          │ │
│             │                       │   review/published)  │        │ created_at     │ │
│             │                       │ is_featured (bool)   │        └────────────────┘ │
│             │                       │ views, likes         │                           │
│             │                       │ reading_time         │                           │
│             │                       │ published_at         │                           │
│             │                       └──────────┬───────────┘                           │
│             │                                  │                                       │
│             ├─────────────────┬────────────────┼────────────────┬──────────────────────┤
│             ▼                 ▼                ▼                ▼                      ▼
│  ┌────────────────────┐ ┌───────────┐ ┌────────────────┐ ┌───────────┐ ┌─────────────┐│
│  │   article_likes    │ │ bookmarks │ │  comment_likes │ │  follows  │ │notifications││
│  ├────────────────────┤ ├───────────┤ ├────────────────┤ ├───────────┤ ├─────────────┤│
│  │ id (PK)            │ │ id (PK)   │ │ id (PK)        │ │ id (PK)   │ │ id (PK)     ││
│  │ user_id (FK)       │ │ user_id   │ │ user_id (FK)   │ │ user_id   │ │ user_id (FK)││
│  │ article_id (FK)    │ │ article_id│ │ comment_id (FK)│ │ author_id │ │ type, data  ││
│  │ created_at         │ │ created_at│ │ created_at     │ │ created_at│ │ read_at     ││
│  └────────────────────┘ └───────────┘ └────────────────┘ └───────────┘ └─────────────┘│
│                                                                                        │
│  ┌────────────────────┐ ┌───────────────────────┐ ┌──────────────────┐ ┌─────────────┐│
│  │   activity_logs    │ │ newsletter_subscribers│ │ contact_messages │ │testimonials ││
│  ├────────────────────┤ ├───────────────────────┤ ├──────────────────┤ ├─────────────┤│
│  │ id (PK)            │ │ id (PK)               │ │ id (PK)          │ │ id (PK)     ││
│  │ user_id (FK, opt)  │ │ email (UQ)            │ │ name, email      │ │ name, role  ││
│  │ action, model_type │ │ is_active (bool)      │ │ subject, message │ │ content     ││
│  │ details (JSON), ip │ │ created_at            │ │ status (read)    │ │ avatar, stars│
│  └────────────────────┘ └───────────────────────┘ └──────────────────┘ └─────────────┘│
│                                                                                        │
│  ┌───────────────────────────────┐ ┌──────────────────────────┐ ┌─────────────────────┐│
│  │    personal_access_tokens     │ │  password_reset_tokens   │ │      sessions       ││
│  ├───────────────────────────────┤ ├──────────────────────────┤ ├─────────────────────┤│
│  │ id (PK), tokenable_id (FK)    │ │ email (PK), token, created││ id (PK), payload    ││
│  │ name, token (UQ), abilities   │ └──────────────────────────┘ └─────────────────────┘│
│  └───────────────────────────────┘                                                     │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## Design System & UI Tokens

BlogHub implements an editorial-inspired design system rooted in classical typographic proportions and an 8px spatial grid.

### Color Tokens

| Token Name | Light Mode | Dark Mode | Semantic Role |
|:---|:---|:---|:---|
| `--color-primary` | `#1F2A44` | `#9BB7E7` | Navy headers, primary brand identity, active tabs |
| `--color-accent` | `#C8461F` | `#E85B33` | Editorial terracotta, primary action CTAs, highlight badges |
| `--color-secondary` | `#3F5A45` | `#6EA378` | Sage green, success confirmations, approval indicators |
| `--color-bg` | `#FBFAF7` | `#0D1117` | Cream paper background (Light) / Carbon dark (Dark) |
| `--color-surface` | `#FFFFFF` | `#161D2A` | Elevated cards, input fields, dropdown containers |
| `--color-text` | `#14171F` | `#F1F4FA` | High-contrast long-form reading typography |
| `--color-text-muted` | `#575E6C` | `#9DA7B8` | Subtitles, reading metadata, timestamp indicators |
| `--color-border` | `#D8D5CC` | `#2B374D` | Editorial hairline dividers and card boundaries |

### Typography Scale

- **Headings (H1–H3)**: `Fraunces` (High-contrast transitional serif)
- **Body & Controls**: `Inter` (Optimized for digital screen legibility)
- **Metadata & Tags**: `IBM Plex Mono` (Precision monospace for reading times, tags, and stats)

---

## Folder Structure

```
Bloghub/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── Reports/
│   │   │   │       ├── ActivityReportController.php    # Audit log filtering & CSV export
│   │   │   │       ├── ApprovalReportController.php    # Author & article approval reports
│   │   │   │       ├── ArticleReportController.php     # Publishing by author/category
│   │   │   │       ├── AuthorReportController.php      # User onboarding & activity metrics
│   │   │   │       ├── EngagementReportController.php  # Views, reads & reaction analytics
│   │   │   │       └── ReportsDashboardController.php  # Unified reports overview
│   │   │   ├── AdminController.php                     # Central admin command center
│   │   │   ├── ArticleController.php                   # Public article reader & filters
│   │   │   ├── AuthController.php                      # Web login, register, logout
│   │   │   ├── AuthorController.php                    # Public author index & profile
│   │   │   ├── BlogDetailController.php                # Article detail view with thread
│   │   │   ├── CategoryController.php                  # Public categories listing
│   │   │   ├── CommentController.php                   # Comment submission & moderation
│   │   │   ├── ContactController.php                   # Contact form handling
│   │   │   ├── DashboardController.php                 # Author workspace & post editor
│   │   │   ├── FavoritesController.php                 # Reader library & favorites dashboard
│   │   │   ├── HomeController.php                      # Homepage curation & newsletter
│   │   │   ├── NotificationController.php              # Notification read management
│   │   │   ├── OtpController.php                       # 6-digit OTP password reset flow
│   │   │   ├── ProfileController.php                   # User profile & author application
│   │   │   ├── SearchController.php                    # Search query & autocomplete
│   │   │   ├── ToggleController.php                    # Like, bookmark, follow toggles
│   │   │   └── Api/                                    # ── REST API v1 CONTROLLERS ──
│   │   │       ├── ArticleController.php               # API Article CRUD & interactions
│   │   │       ├── AuthController.php                  # Sanctum register, login, OTP
│   │   │       ├── AuthorController.php                # Author API & author dashboard
│   │   │       ├── CategoryController.php              # Category endpoints
│   │   │       ├── CommentController.php               # Comment posting & reactions
│   │   │       ├── GeneralController.php               # Stats, contact, newsletter
│   │   │       ├── NotificationController.php          # In-app notifications API
│   │   │       ├── PostController.php                  # Legacy feed compatibility
│   │   │       ├── ProfileController.php               # Account & favorites API
│   │   │       └── SearchController.php                # Autocomplete & search API
│   │   └── Middleware/
│   │       ├── Authenticate.php                        # Auth & active account verification
│   │       ├── RoleMiddleware.php                      # RBAC role verification (admin/author)
│   │       ├── RateLimit.php                           # Throttle guard
│   │       └── SecurityHeaders.php                     # CSP, HSTS, X-Frame-Options
│   ├── Models/
│   │   ├── ActivityLog.php                             # Platform audit trail
│   │   ├── Article.php                                 # Post entity, scopes & metrics
│   │   ├── Category.php                                # Taxonomies
│   │   ├── Comment.php                                 # Threaded comments & replies
│   │   ├── ContactMessage.php                          # Reader inquiries
│   │   ├── NewsletterSubscriber.php                    # Email subscriptions
│   │   ├── Testimonial.php                             # Curated reviews
│   │   └── User.php                                    # Users, roles, avatars & relations
│   └── Support/
│       └── HtmlSanitizer.php                           # Allowlist-based HTML sanitizer
├── database/
│   ├── database.sqlite                                 # Local development database
│   ├── migrations/                                     # 22 schema migrations
│   └── seeders/
│       ├── DatabaseSeeder.php                          # Master seeder with rich content
│       └── ActivityLogSeeder.php                       # Initial audit log generator
├── public/
│   ├── manifest.json                                   # PWA install configuration
│   ├── sw.js                                           # Service Worker offline cache
│   └── assets/
│       ├── css/                                        # style.css, components.css, dark-mode.css
│       └── js/                                         # main.js, components.js, data.js
├── resources/
│   └── views/
│       ├── layouts/app.blade.php                       # Master HTML5 layout with prefetch
│       ├── partials/                                   # Navbar, footer, search overlay
│       └── pages/
│           ├── home.blade.php                          # Curated editorial home
│           ├── blogs.blade.php                         # Listing with filters & sorting
│           ├── blog-details.blade.php                  # Article view & comments thread
│           ├── favorites.blade.php                     # Reader library hub
│           ├── profile-settings.blade.php              # Account management
│           ├── admin/                                  # Admin portal & reports views
│           │   ├── overview.blade.php
│           │   ├── posts.blade.php
│           │   ├── users.blade.php
│           │   ├── categories.blade.php
│           │   ├── comments.blade.php
│           │   ├── messages.blade.php
│           │   ├── subscribers.blade.php
│           │   ├── testimonials.blade.php
│           │   └── reports/                            # 11 reporting dashboard views
│           └── dashboard/                              # Author portal views
│               ├── overview.blade.php
│               ├── posts.blade.php
│               ├── post-form.blade.php
│               └── comments.blade.php
├── routes/
│   ├── api.php                                         # Versioned REST API v1 routes
│   ├── web.php                                         # Server-rendered web routes
│   └── console.php                                     # CLI maintenance tasks
└── tests/
    └── Feature/                                        # 17 Feature test suites
```

---

## Demo Credentials

The platform ships pre-configured with realistic editorial demo accounts created via `php artisan db:seed`.

All demo accounts share the standard password: **`password`**

| Role | Name | Email | Password | Access Level |
|:---|:---|:---|:---|:---|
| **Admin** | Admin User | `admin@bloghub.com` | `password` | Full Admin Control Center (`/admin`) + Reports Engine |
| **Author** | Elena Rostova | `elena@bloghub.com` | `password` | Author Dashboard (`/dashboard`), Post Creation & Moderation |
| **Author** | Marcus Chen | `marcus@bloghub.com` | `password` | Author Dashboard (`/dashboard`), Post Creation & Moderation |
| **Author** | Dr. Sarah Jenkins | `sarah@bloghub.com` | `password` | Author Dashboard (`/dashboard`), Post Creation & Moderation |
| **Reader** | Reader One | `reader1@bloghub.com` | `password` | Reading, Threaded Comments, Bookmarks & Following |
| **Reader** | Reader Two | `reader2@bloghub.com` | `password` | Reading, Threaded Comments, Bookmarks & Following |

---

## Installation & Local Setup

### System Prerequisites

- **PHP**: `^8.2` with `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` extensions
- **Composer**: `^2.0`
- **Node.js**: `^18.0` & **npm**: `^9.0`
- **Database**: SQLite (default, zero-configuration) or MySQL / PostgreSQL

### Quick Setup (One Command)

BlogHub provides an automated setup script defined in `composer.json`:

```bash
composer run setup
```

### Step-by-Step Manual Installation

```bash
# 1. Clone the repository
git clone https://github.com/Saniya378/bloghub.git
cd bloghub

# 2. Install PHP backend dependencies
composer install

# 3. Create environment configuration
cp .env.example .env

# 4. Generate unique application encryption key
php artisan key:generate

# 5. Initialize the SQLite database file
# On Windows PowerShell:
New-Item -ItemType File -Force -Path database/database.sqlite
# On Linux/macOS:
touch database/database.sqlite

# 6. Execute database migrations and seed realistic demo data
php artisan migrate --seed

# 7. Create storage symlink for uploaded avatars and banners
php artisan storage:link

# 8. Install frontend npm dependencies
npm install

# 9. Build frontend assets for production
npm run build

# 10. Start the Laravel development server
php artisan serve
```

Access the application in your browser at: **`http://127.0.0.1:8000`**

---

## Environment Configuration

Key configuration parameters inside `.env`:

```ini
APP_NAME=BlogHub
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Database Configuration (Default: SQLite)
DB_CONNECTION=sqlite
# For MySQL uncomment and configure:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bloghub
# DB_USERNAME=root
# DB_PASSWORD=

# Session, Cache & Queue Drivers
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail Configuration (Set to 'log' for local testing)
MAIL_MAILER=log
```

---

## Testing & Quality Assurance

BlogHub includes 17 automated feature test suites guaranteeing regression prevention, security boundaries, and API contract compliance.

### Executing the Test Suites

```bash
# Run the entire test suite
php artisan test

# Run only the API endpoints test suite
php artisan test --filter=ApiEndpointsTest

# Run only the Author API tests
php artisan test --filter=AuthorApiTest

# Run reports module tests
php artisan test --filter=ReportsModuleTest
```

### Complete Test Suites Inventory

| Test Suite | Purpose & Coverage |
|:---|:---|
| `ApiEndpointsTest.php` | Sanctum authentication, registration, article/comment API endpoints, headers |
| `AuthorApiTest.php` | Author profile endpoints, follow states, author dashboard metrics via API |
| `AuthTest.php` | Web registration, credential validation, rate limiting, session protection |
| `BlogFilteringTest.php` | Search queries, category filtering, tag lookups, SQL injection defense |
| `CommentTest.php` | Comment submission, spam protection, authorization checks |
| `ThreadedCommentsTest.php` | Recursive parent-child replies, nested tree rendering, deletion cascade |
| `FavoritesDashboardTest.php` | Reader library tabs, liked articles, bookmarks, followed author lists |
| `FollowAndLiveFeedTest.php` | Author following mechanics, dynamic live feed personalization |
| `LiveTimestampsTest.php` | Accurate relative timestamp parsing and localization |
| `OtpFlowTest.php` | 6-digit OTP code dispatch, expiration checks, password reset execution |
| `PostAuthorizationTest.php` | Author-only editing, admin moderation override, mass-assignment defense |
| `ProfileFollowingTest.php` | User settings updates, bio/tagline edits, following lists |
| `ReportsModuleTest.php` | Reports controller permissions, data aggregation, CSV generation |
| `ReportsTest.php` | Reports routing and admin access control |
| `ReviewWorkflowTest.php` | Article lifecycle: draft submission, admin approval, rejection with notes |
| `ToggleTest.php` | Idempotent toggles for likes, bookmarks, and author follows |
| `ExampleTest.php` | Basic application health check |

---

## Security Architecture

BlogHub applies defensive engineering principles across the application lifecycle:

1. **HTML Sanitization**: Custom `HtmlSanitizer` utility strips dangerous script tags, event handlers (`onload`, `onerror`), and malicious attributes from article bodies while preserving legitimate typography markup (`<blockquote>`, `<code>`, `<h3>`, `<img>`).
2. **Rate Limiting**: Critical endpoints (login, registration, OTP requests, contact inquiries) are guarded with Laravel's `throttle` middleware to prevent brute-force attacks and abuse.
3. **Cross-Site Request Forgery (CSRF)**: All state-changing web requests require valid CSRF tokens automatically verified by `VerifyCsrfToken` middleware.
4. **Token Revocation (Sanctum)**: API tokens are hashed, scoped, and immediately revokable upon logout.
5. **Role-Based Route Guards**: Routes are segregated by `auth` and `role:{role}` middleware; unauthorized access triggers immediate redirect or 403 Forbidden responses.
6. **SQL Injection Prevention**: All queries utilize Eloquent ORM or parameterized PDO bindings.
7. **Security Headers**: Custom `SecurityHeaders` middleware injects `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, and strict referrer policies on all responses.

---

## Screenshots

| Desktop View | Dark Mode |
|:---:|:---:|
| ![Home Page](screenshots/home.png)<br>*Curated Editorial Homepage* | ![Dark Mode](screenshots/dark-mode.png)<br>*High-Contrast Carbon Dark Mode* |

| Author Dashboard | Admin Reports |
|:---:|:---:|
| ![Author Dashboard](screenshots/blogs.png)<br>*Author Publishing Workspace* | ![Admin Reports](screenshots/author-profile.png)<br>*Analytics & Business Intelligence* |

---

## License & Credits

- **License**: Released under the permissive [MIT License](https://opensource.org/licenses/MIT).
- **Core Framework**: [Laravel 12](https://laravel.com) by Taylor Otwell and the Laravel community.
- **Frontend**: [Bootstrap 5](https://getbootstrap.com) with custom vanilla CSS design tokens.
- **Iconography**: [Font Awesome 6](https://fontawesome.com).
- **Typography**: [Fraunces](https://fonts.google.com/specimen/Fraunces) & [Inter](https://fonts.google.com/specimen/Inter) via Google Fonts.

---

*Engineered with precision for modern journalism and thoughtful publishing.*
