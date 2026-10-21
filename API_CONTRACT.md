# BlogHub REST API Contract — Author Mobile App (Flutter)

> **Audience**: Flutter developers building the mobile application for users with role **`author`**.  
> **Source of Truth**: Extracted directly from live backend source code (`routes/api.php`, controllers, models, and sanitizers).  
> **Base URL**: `http://<HOST>:8000/api/v1` (e.g., `http://10.0.2.2:8000/api/v1` for Android Emulator, `http://localhost:8000/api/v1` for iOS Simulator).  
> **Protocol**: All requests accept and return `application/json` (except multipart file upload endpoints which use `multipart/form-data`).  
> **Authentication Scheme**: HTTP Bearer Token (`Authorization: Bearer <token>`). Tokens are issued via Sanctum with an expiration of **43,200 minutes (30 days)**.

---

## Table of Contents
1. [Core Architectural Questions (Q&A)](#1-core-architectural-questions-qa)
2. [Article Lifecycle & Status Rules](#2-article-lifecycle--status-rules)
3. [Comment Moderation Workflow](#3-comment-moderation-workflow)
4. [HTML Sanitization & Content Rules](#4-html-sanitization--content-rules)
5. [Standard Error & Rate Limiting Responses](#5-standard-error--rate-limiting-responses)
6. [Authentication Endpoints](#6-authentication-endpoints)
   - [POST /api/v1/auth/register-author](#post-apiv1authregister-author)
   - [POST /api/v1/auth/login](#post-apiv1authlogin)
   - [GET /api/v1/auth/me](#get-apiv1authme)
   - [POST /api/v1/auth/logout](#post-apiv1authlogout)
7. [Categories Endpoints](#7-categories-endpoints)
   - [GET /api/v1/categories](#get-apiv1categories)
8. [Article Management Endpoints (Author)](#8-article-management-endpoints-author)
   - [GET /api/v1/profile/my-articles](#get-apiv1profilemy-articles)
   - [GET /api/v1/articles/{identifier}](#get-apiv1articlesidentifier)
   - [POST /api/v1/articles (Create)](#post-apiv1articles-create)
   - [POST /api/v1/articles/{id} (Update with `_method=PUT`)](#post-apiv1articlesid-update-with-_methodput)
   - [POST /api/v1/articles/{id}/submit (Submit for Review)](#post-apiv1articlesidsubmit-submit-for-review)
   - [DELETE /api/v1/articles/{id} (Delete)](#delete-apiv1articlesid-delete)
9. [Author Comment Moderation Endpoints](#9-author-comment-moderation-endpoints)
   - [GET /api/v1/author/comments](#get-apiv1authorcomments)
   - [POST /api/v1/author/comments/{id}/approve](#post-apiv1authorcommentsidapprove)
   - [DELETE /api/v1/author/comments/{id}](#delete-apiv1authorcommentsid)
10. [Profile Management Endpoints](#10-profile-management-endpoints)
    - [GET /api/v1/profile](#get-apiv1profile)
    - [POST /api/v1/profile/update](#post-apiv1profileupdate)
    - [POST /api/v1/profile/password](#post-apiv1profilepassword)
11. [Local Development & Flutter Setup Guide](#11-local-development--flutter-setup-guide)

---

## 1. Core Architectural Questions (Q&A)

### (a) Are image URLs in responses full or relative?
**They are relative paths starting with `/storage/` when uploaded via multipart file**, e.g.:
- `"/storage/articles/filename.jpg"`
- `"/storage/avatars/filename.png"`
- `"/storage/covers/filename.jpg"`

*(Note: If seeded or passed via `image_url` string, they may be full URLs like `https://images.unsplash.com/...`).*  
**Flutter Implementation Rule:** Always resolve URLs using a helper:
```dart
String resolveImageUrl(String? path, String baseUrl) {
  if (path == null || path.isEmpty) return '';
  if (path.startsWith('http://') || path.startsWith('https://')) return path;
  final cleanBase = baseUrl.endsWith('/') ? baseUrl.substring(0, baseUrl.length - 1) : baseUrl;
  final cleanPath = path.startsWith('/') ? path : '/$path';
  return '$cleanBase$cleanPath';
}
```

### (b) Category in article response: object or slug? What does GET /categories return?
1. In article responses (e.g. `GET /api/v1/articles/{id}`, `POST /api/v1/articles`, `GET /api/v1/profile/my-articles`), the category is returned as a **slug string attribute** named `category_slug` (e.g. `"technology"`, `"travel"`), **not** an object.
2. `GET /api/v1/categories` returns a list of category objects containing:
   - `id` (int)
   - `name` (string)
   - `slug` (string)
   - `icon` (string, FontAwesome class e.g. `"fa-laptop-code"`)
   - `description` (string)
   - `articles_count` (int)

### (c) Pagination format
Across paginated endpoints, pagination metadata is enclosed under the `"pagination"` object:
```json
"pagination": {
    "current_page": 1,
    "last_page": 4,
    "per_page": 10,
    "total": 35,
    "has_more": true
}
```
- `current_page`: (int) Current page number (1-based).
- `last_page`: (int) Total pages available.
- `per_page`: (int) Number of items per page.
- `total`: (int) Total count of items matching the query.
- `has_more`: (bool, optional) Whether additional pages exist.

### (d) Tags format in responses
In Eloquent, `tags` is cast to an array (`'tags' => 'array'`).
- In JSON responses, `tags` is serialized as a **JSON array of strings**, e.g.:
  `"tags": ["flutter", "dart", "mobile"]` (or `[]` when empty).
- When submitting create or update requests, the backend accepts **either** a JSON array (`["tag1", "tag2"]`) or a comma-separated string (`"tag1, tag2"`).

### (e) What happens if a reader or admin token calls author endpoints?
- **`GET /api/v1/profile/my-articles`**:
  - Scoped to `$user->id`.
  - **Admin token**: Returns 200 with the admin's own articles.
  - **Reader token**: Returns 200 with empty list `[]` (readers have no articles).
- **`GET /api/v1/author/comments` & Author Moderation (`/api/v1/author/comments/{id}/*`)**:
  - Protected with role check: `$user->role !== 'author' && $user->role !== 'admin'`.
  - **Reader token**: Returns **403 Forbidden** with `{"success": false, "message": "You must be registered as an author to moderate comments."}`.
  - **Admin token**: Allowed (200 OK).
- **Author editing another author's article**: Returns **403 Forbidden** with `{"success": false, "message": "Unauthorized to edit this article."}`.
- **Author moderating comments on another author's article**: Returns **403 Forbidden** with `{"success": false, "message": "You can only moderate comments on your own articles."}`.

### (f) Does login return the token and user together? Exact keys
**YES.** Under `data`:
```json
{
    "success": true,
    "message": "Logged in successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "Jane Author",
            "username": "jane-author",
            "email": "author@bloghub.com",
            "role": "author",
            "avatar": "/storage/avatars/avatar.jpg",
            "bio": "Mobile Engineer & Writer",
            "specialty": "Flutter",
            "tagline": "Building high performance apps"
        },
        "token": "1|abcdef123456789...",
        "token_type": "Bearer"
    }
}
```
Exact keys in `data`: **`user`**, **`token`**, **`token_type`**.

### (g) What does delete article allow for authors (which statuses)?
- `DELETE /api/v1/articles/{id}` checks **only ownership**: `$article->user_id === $user->id`.
- An author can delete **ANY** of their own articles regardless of status (`draft`, `pending`, `rejected`, or `published`).
- Admins can delete any article across the platform.

---

## 2. Article Lifecycle & Status Rules

```
       [Author creates]
              │
              ▼
          ┌───────┐      submit() / submitForReview()
          │ draft │ ───────────────────────────────────┐
          └───────┘                                    │
              ▲                                        ▼
              │ edit & save                       ┌─────────┐
              │                                   │ pending │
              │                                   └─────────┘
              │                                        │
              │ reject(note)            approve()      │
         ┌──────────┐                                  ▼
         │ rejected │ ◀───────────────────────── ┌───────────┐
         └──────────┘                            │ published │
                                                 └───────────┘
```

| Status | Label | Author Can Edit? | Author Can Submit? | Notes |
|---|---|---|---|---|
| `draft` | Draft | **YES** | **YES** | Author's working copy. Visible only to author and admin. |
| `pending` | In Review | **NO** (Returns 403) | **NO** (Returns 422) | Locked while under admin review. Author cannot edit. |
| `published` | Published | **NO** (Returns 403) | **NO** (Returns 422) | Live public article. Authors cannot directly edit published articles via API. |
| `rejected` | Rejected | **YES** (transitions to `draft`) | **YES** (after saving) | Contains `review_note` from admin explaining rejection. When author edits and saves, status becomes `draft` while `review_note` stays visible until resubmission. |

### Status Enforcement Rules for Authors:
1. **Create (`POST /articles`)**:
   - Status may only be `draft` (default) or `pending`.
   - If `status: "published"` is submitted by an author, returns **422 Unprocessable Entity**.
   - If `status: "pending"` is sent, article is automatically routed through `submitForReview()`.
2. **Update (`POST /articles/{id}` with `_method=PUT`)**:
   - Only permitted if status is `draft` or `rejected`.
   - If status is `pending` or `published`, returns **403 Forbidden** with exact message:  
     `"This article cannot be edited right now."`
   - If author submits `status: "published"`, returns **422 Unprocessable Entity**.
   - When a `rejected` article is edited and saved, its status resets to `draft` so the author can polish it.
3. **Submit (`POST /articles/{id}/submit`)**:
   - Author can only submit from `draft` or `rejected`.
   - Sets status to `pending`, records `submitted_at`, clears `review_note`, and logs an activity event.

---

## 3. Comment Moderation Workflow

- **Reader comments**: Default to status `pending` (moderation queue). They do **not** appear in public threads and do **not** increment public `comment_count`.
- **Author/Admin comments**: Auto-approved (`status: approved`), immediately visible, and increment public `comment_count`.
- **Author Dashboard Moderation**:
  - `GET /api/v1/author/comments`: Returns comments on the logged-in author's own articles (with `?status=pending|approved` filter and pagination 10/page).
  - `POST /api/v1/author/comments/{id}/approve`: Approves comment and increments `comment_count`.
  - `DELETE /api/v1/author/comments/{id}`: Deletes comment and descendant replies, decrementing `comment_count` if it was approved.
  - Another author attempting to approve/delete receives **403 Forbidden**.

---

## 4. HTML Sanitization & Content Rules

Content in `content` and `excerpt` is filtered through server-side `HtmlSanitizer::sanitize()`:

### Allowed HTML Tags:
- **Structural**: `p`, `br`, `hr`, `h1`, `h2`, `h3`, `h4`, `h5`, `h6`, `div`, `span`
- **Formatting**: `strong`, `b`, `em`, `i`, `u`, `s`, `strike`, `del`, `ins`, `sup`, `sub`
- **Lists**: `ul`, `ol`, `li`
- **Quotes & Code**: `blockquote`, `pre`, `code`
- **Tables**: `table`, `thead`, `tbody`, `tr`, `th`, `td`
- **Media & Links**: `a`, `img`

### Allowed Tag Attributes:
- `a`: `href`, `title`, `target`, `rel`
- `img`: `src`, `alt`, `title`, `width`, `height`, `loading`
- `code`, `pre`: `class`
- `div`, `span`, `p`: `class`, `id`
- `h1`–`h6`: `id`
- `table`: `class`
- `th`, `td`: `colspan`, `rowspan`

### Security Restrictions:
- **Disallowed / Stripped Tags**: `<script>`, `<style>`, `<iframe>`, `<object>`, `<embed>`, `<applet>`, `<form>`, `<input>`, `<button>`. (These and their inner contents are removed).
- **Disallowed Attributes**: All `on*` event handlers (e.g. `onclick`, `onload`, `onerror`) and `style` attributes are removed.
- **Allowed URL Schemes**: `http`, `https`, `mailto`, `tel`. (Dangerous schemes like `javascript:` and `data:` are stripped).

---

## 5. Standard Error & Rate Limiting Responses

### 401 Unauthorized
Returned when missing or invalid Bearer token:
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
Returned when user lacks permissions or article is in an un-editable state:
```json
{
    "success": false,
    "message": "This article cannot be edited right now."
}
```
*(Or `"You can only moderate comments on your own articles."`)*

### 422 Unprocessable Entity (Validation Errors)
Form validation errors always include the `errors` dictionary:
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "title": [
            "The title field is required."
        ],
        "category_slug": [
            "The category slug field is required."
        ]
    }
}
```

### 429 Too Many Requests (Rate Limiting)
All throttled write routes return JSON with standard keys and HTTP header `Retry-After`:
```json
{
    "success": false,
    "message": "Too many requests. Please try again in 58 seconds."
}
```

---

## 6. Authentication Endpoints

### POST `/api/v1/auth/register-author`
- **Auth**: None (Public)
- **Role Assigned**: Automatically sets `role: "author"` and `is_active: true`.
- **Username**: Auto-generated from `name` (clean slug with collision counter suffix).
- **Email Normalization**: Automatically trimmed and lowercased before validation and storage.
- **Rate Limit**: 10 requests/min (`throttle:10,1`)
- **Request Body** (`application/json`):
  | Field | Type | Required | Description |
  |---|---|---|---|
  | `name` | string | **Yes** | Author display name (max 255 chars). |
  | `email` | string | **Yes** | Valid unique email (max 255 chars). Trimmed and lowercased. |
  | `password` | string | **Yes** | Account password (min 8 chars). |
  | `password_confirmation` | string | **Yes** | Must match `password`. |

- **Success Response (201 Created)**:
```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "user": {
            "id": 15,
            "name": "Sarah Connor",
            "username": "sarah-connor",
            "email": "sarah.connor@example.com",
            "role": "author",
            "is_active": true,
            "created_at": "2026-10-21T07:35:00.000000Z",
            "updated_at": "2026-10-21T07:35:00.000000Z"
        },
        "token": "12|a9ZbC01dE...",
        "token_type": "Bearer"
    }
}
```
- **Error Responses**:
  - `422 Unprocessable Entity` (Validation error):
    ```json
    {
        "success": false,
        "message": "Validation error",
        "errors": {
            "email": ["The email has already been taken."],
            "password": ["The password field confirmation does not match."]
        }
    }
    ```
  - `429 Too Many Requests` (Rate limit exceeded):
    ```json
    {
        "success": false,
        "message": "Too many requests. Please try again in 58 seconds."
    }
    ```

### POST `/api/v1/auth/login`
- **Auth**: None (Public)
- **Rate Limit**: 5 attempts/min (Internal IP throttle)
- **Request Body** (`application/json`):
  | Field | Type | Required | Description |
  |---|---|---|---|
  | `email` | string | **Yes** | Email or username |
  | `password` | string | **Yes** | Account password |
  | `device_name` | string | No | Device identifier (default: `"mobile_app"`) |

- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Logged in successfully",
    "data": {
        "user": {
            "id": 2,
            "name": "Alex Author",
            "username": "alex-author",
            "email": "alex@example.com",
            "role": "author",
            "avatar": "/storage/avatars/user_2.jpg",
            "cover_image": null,
            "bio": "Tech blogger & Flutter enthusiast",
            "tagline": "Coding the future",
            "specialty": "Mobile Development",
            "is_active": true
        },
        "token": "3|KjU1A8u9gQ6Z4y78...",
        "token_type": "Bearer"
    }
}
```
- **Error Responses**:
  - `401`: `{"success": false, "message": "Invalid credentials provided."}`
  - `403`: `{"success": false, "message": "Your account has been deactivated. Please contact support."}`
  - `422`: `{"success": false, "message": "Validation error", "errors": {"email": ["The email field is required."]}}`
  - `429`: `{"success": false, "message": "Too many login attempts. Please try again in 45 seconds."}`

---

### GET `/api/v1/auth/me`
- **Auth**: Bearer Token
- **Role**: Any authenticated user
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 2,
            "name": "Alex Author",
            "username": "alex-author",
            "email": "alex@example.com",
            "role": "author",
            "avatar": "/storage/avatars/user_2.jpg",
            "cover_image": null,
            "bio": "Tech blogger & Flutter enthusiast",
            "tagline": "Coding the future",
            "specialty": "Mobile Development"
        },
        "unread_notifications_count": 3
    }
}
```
- **Error Responses**:
  - `401`: `{"message": "Unauthenticated."}`

---

### POST `/api/v1/auth/logout`
- **Auth**: Bearer Token
- **Role**: Any authenticated user
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

## 7. Categories Endpoints

### GET `/api/v1/categories`
- **Auth**: None (Public)
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Technology",
            "slug": "technology",
            "icon": "fa-laptop-code",
            "description": "Latest updates in software, mobile, and AI.",
            "articles_count": 24
        },
        {
            "id": 2,
            "name": "Design & UI/UX",
            "slug": "design",
            "icon": "fa-paint-brush",
            "description": "Design systems, typography, and aesthetics.",
            "articles_count": 14
        }
    ]
}
```

---

## 8. Article Management Endpoints (Author)

### GET `/api/v1/profile/my-articles`
Returns the logged-in author's **own** articles across all statuses.
- **Auth**: Bearer Token
- **Role**: `author` or `admin`
- **Query Parameters**:
  | Param | Type | Required | Description |
  |---|---|---|---|
  | `status` | string | No | Filter by `draft`, `pending`, `published`, or `rejected`. Omit for all. |
  | `page` | integer | No | Page number (default: 1) |
  | `per_page` | integer | No | Items per page (default: 10, max: 30) |

- **Success Response (200 OK)**:
```json
{
    "success": true,
    "data": [
        {
            "id": 45,
            "user_id": 2,
            "title": "Flutter Clean Architecture in 2026",
            "slug": "flutter-clean-architecture-in-2026",
            "excerpt": "A deep dive into state management and domain layers.",
            "content": "<p>Architecture patterns in mobile apps...</p>",
            "category_slug": "technology",
            "tags": ["flutter", "architecture", "dart"],
            "image": "/storage/articles/flutter_arch.jpg",
            "status": "pending",
            "views": 0,
            "likes": 0,
            "reading_time": 4,
            "published_at": null,
            "submitted_at": "2026-10-20T10:15:30.000000Z",
            "reviewed_at": null,
            "reviewed_by": null,
            "review_note": null,
            "created_at": "2026-10-20T10:00:00.000000Z",
            "updated_at": "2026-10-20T10:15:30.000000Z",
            "comments_count": 0
        },
        {
            "id": 32,
            "user_id": 2,
            "title": "Dart 3 Records and Patterns",
            "slug": "dart-3-records-and-patterns",
            "excerpt": "How to simplify model parsing with records.",
            "content": "<p>Records allow tuples in Dart...</p>",
            "category_slug": "technology",
            "tags": ["dart", "language"],
            "image": null,
            "status": "rejected",
            "views": 0,
            "likes": 0,
            "reading_time": 3,
            "published_at": null,
            "submitted_at": "2026-10-18T09:00:00.000000Z",
            "reviewed_at": "2026-10-18T14:20:00.000000Z",
            "reviewed_by": 1,
            "review_note": "Please add more code examples for pattern matching.",
            "created_at": "2026-10-18T08:30:00.000000Z",
            "updated_at": "2026-10-18T14:20:00.000000Z",
            "comments_count": 0
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 2
    }
}
```

---

### GET `/api/v1/articles/{identifier}`
Fetch article details by ID or Slug.
- **Auth**: Optional (Public). If Bearer token is provided, returns user interaction flags (`is_liked`, `is_bookmarked`, `is_following_author`).
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "data": {
        "id": 45,
        "user_id": 2,
        "title": "Flutter Clean Architecture in 2026",
        "slug": "flutter-clean-architecture-in-2026",
        "excerpt": "A deep dive into state management and domain layers.",
        "content": "<p>Architecture patterns in mobile apps...</p>",
        "category_slug": "technology",
        "tags": ["flutter", "architecture"],
        "image": "/storage/articles/flutter_arch.jpg",
        "status": "published",
        "views": 154,
        "likes": 23,
        "comment_count": 2,
        "comments_count": 2,
        "reading_time": 4,
        "published_at": "2026-10-19T10:00:00.000000Z",
        "user": {
            "id": 2,
            "name": "Alex Author",
            "username": "alex-author",
            "avatar": "/storage/avatars/user_2.jpg",
            "role": "author",
            "bio": "Tech blogger & Flutter enthusiast",
            "specialty": "Mobile Development",
            "tagline": "Coding the future"
        },
        "comments": [
            {
                "id": 101,
                "user_id": 4,
                "article_id": 45,
                "parent_id": null,
                "content": "Terrific article!",
                "status": "approved",
                "likes": 3,
                "user": {
                    "id": 4,
                    "name": "Sam Reader",
                    "username": "sam-reader",
                    "avatar": "/storage/avatars/sam.png"
                },
                "replies": []
            }
        ],
        "is_liked": false,
        "is_bookmarked": true,
        "is_following_author": false,
        "related_articles": []
    }
}
```
- **Error Responses**:
  - `404`: `{"success": false, "message": "Article not found."}`

---

### POST `/api/v1/articles` (Create)
Creates a new article. Accepts either JSON or `multipart/form-data` (for cover image upload).
- **Auth**: Bearer Token
- **Role**: `author` or `admin`
- **Rate Limit**: 60 requests/min
- **Request Fields**:
  | Field | Type | Required | Rules & Description |
  |---|---|---|---|
  | `title` | string | **Yes** | Max 255 chars |
  | `content` | string | **Yes** | HTML or markdown body. Will be sanitized. |
  | `category_slug` | string | **Yes** | Valid category slug (e.g. `"technology"`) |
  | `excerpt` | string | No | Max 500 chars. Auto-generated from content if omitted. |
  | `tags` | array or string | No | e.g. `["flutter", "mobile"]` or `"flutter, mobile"` |
  | `status` | string | No | Allowed for author: `"draft"` (default) or `"pending"`. **`"published"` returns 422**. |
  | `image` | file | No | Image file (jpg, jpeg, png, webp, max: 5120 KB) |
  | `image_url` | string | No | URL to remote cover image |

- **Success Response (201 Created)**:
```json
{
    "success": true,
    "message": "Article created successfully.",
    "data": {
        "id": 46,
        "user_id": 2,
        "title": "Getting Started with Flutter",
        "slug": "getting-started-with-flutter",
        "excerpt": "Introduction to widgets and layout in Flutter.",
        "content": "<p>Flutter is Google's UI toolkit...</p>",
        "category_slug": "technology",
        "tags": ["flutter", "mobile"],
        "image": "/storage/articles/flutter_start.jpg",
        "reading_time": 2,
        "status": "draft",
        "published_at": null,
        "created_at": "2026-10-20T11:00:00.000000Z",
        "updated_at": "2026-10-20T11:00:00.000000Z"
    }
}
```
- **Error Responses**:
  - `422` (Author sends `status: "published"`):
    ```json
    {
        "success": false,
        "message": "Validation error",
        "errors": {
            "status": [
                "Authors cannot publish articles directly."
            ]
        }
    }
    ```
  - `422` (Validation failure):
    ```json
    {
        "success": false,
        "message": "Validation error",
        "errors": {
            "title": ["The title field is required."],
            "content": ["The content field is required."]
        }
    }
    ```
  - `429`: `{"success": false, "message": "Too many requests. Please try again in 50 seconds."}`

---

### POST `/api/v1/articles/{id}` (Update with `_method=PUT`)
Updates an existing article.
> **IMPORTANT FOR FLUTTER MULTIPART UPLOADS**: PHP does not natively populate `$_FILES` for HTTP `PUT` requests with `multipart/form-data`. **Always send as HTTP `POST` with form field `_method: "PUT"`** (or use the alternate route `POST /api/v1/articles/{id}/update`).

- **Auth**: Bearer Token
- **Role**: Owner (`author`) or `admin`
- **Rate Limit**: 60 requests/min
- **Eligibility**: **Only draft or rejected articles can be edited**. If the article is `pending` or `published`, it returns `403`.
- **Request Fields** (`multipart/form-data` or `application/json`):
  | Field | Type | Required | Rules & Description |
  |---|---|---|---|
  | `_method` | string | **Yes** (if POST) | Set to `"PUT"` |
  | `title` | string | No | Max 255 chars |
  | `content` | string | No | Sanitized automatically |
  | `category_slug` | string | No | Category slug |
  | `excerpt` | string | No | Max 500 chars |
  | `tags` | array or string | No | e.g. `["flutter", "dart"]` or `"flutter, dart"` |
  | `status` | string | No | Allowed: `"draft"` or `"pending"`. If article was `rejected`, saving automatically sets it to `draft`. |
  | `image` | file | No | New cover image file (max: 5120 KB) |
  | `image_url` | string | No | URL to new cover image |

- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Article updated successfully.",
    "data": {
        "id": 46,
        "user_id": 2,
        "title": "Getting Started with Flutter 3.x",
        "slug": "getting-started-with-flutter-3x",
        "excerpt": "Updated guide to Flutter widgets.",
        "content": "<p>Updated content here...</p>",
        "category_slug": "technology",
        "tags": ["flutter", "dart"],
        "image": "/storage/articles/new_cover.jpg",
        "reading_time": 3,
        "status": "draft",
        "review_note": null,
        "created_at": "2026-10-20T11:00:00.000000Z",
        "updated_at": "2026-10-20T11:30:00.000000Z",
        "user": {
            "id": 2,
            "name": "Alex Author",
            "username": "alex-author",
            "avatar": "/storage/avatars/user_2.jpg"
        }
    }
}
```
- **Error Responses**:
  - `403` (Article is pending or published):
    ```json
    {
        "success": false,
        "message": "This article cannot be edited right now."
    }
    ```
  - `403` (Not owner):
    ```json
    {
        "success": false,
        "message": "Unauthorized to edit this article."
    }
    ```
  - `422` (Author attempts `status: "published"`):
    ```json
    {
        "success": false,
        "message": "Validation error",
        "errors": {
            "status": [
                "Authors cannot publish articles directly."
            ]
        }
    }
    ```

---

### POST `/api/v1/articles/{id}/submit` (Submit for Review)
Submits a `draft` or `rejected` article to the admin review queue.
- **Auth**: Bearer Token
- **Role**: Owner (`author`) or `admin`
- **Rate Limit**: 60 requests/min
- **Request Body**: None required
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Article submitted for review successfully.",
    "data": {
        "id": 46,
        "user_id": 2,
        "title": "Getting Started with Flutter 3.x",
        "slug": "getting-started-with-flutter-3x",
        "status": "pending",
        "submitted_at": "2026-10-20T12:00:00.000000Z",
        "reviewed_at": null,
        "reviewed_by": null,
        "review_note": null,
        "updated_at": "2026-10-20T12:00:00.000000Z"
    }
}
```
- **Error Responses**:
  - `403` (Not owner): `{"success": false, "message": "Unauthorized to submit this article."}`
  - `422` (Not draft or rejected):
    ```json
    {
        "success": false,
        "message": "Article can only be submitted for review from draft or rejected status."
    }
    ```

---

### DELETE `/api/v1/articles/{id}` (Delete)
Deletes an article.
- **Auth**: Bearer Token
- **Role**: Owner (`author`) or `admin`
- **Allowed Statuses**: **Any** status (`draft`, `pending`, `rejected`, or `published`) as long as the user is the owner.
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Article deleted successfully."
}
```
- **Error Responses**:
  - `403` (Not owner): `{"success": false, "message": "Unauthorized to delete this article."}`
  - `404`: `{"success": false, "message": "Article not found."}`

---

## 9. Author Comment Moderation Endpoints

### GET `/api/v1/author/comments`
Lists comments posted on the logged-in author's own articles.
- **Auth**: Bearer Token
- **Role**: `author` or `admin` (Readers receive `403`)
- **Query Parameters**:
  | Param | Type | Required | Description |
  |---|---|---|---|
  | `status` | string | No | Filter by `"pending"` or `"approved"` |
  | `article_id` | integer | No | Filter by specific owned article |
  | `page` | integer | No | Page number (default: 1) |
  | `per_page` | integer | No | Items per page (**default: 10**, max: 50) |

- **Success Response (200 OK)**:
```json
{
    "success": true,
    "data": [
        {
            "id": 128,
            "content": "Can you provide a full repo example for the bloc pattern?",
            "status": "pending",
            "likes": 0,
            "created_at": "2026-10-20T12:30:00.000000Z",
            "user_id": 9,
            "commenter_name": "Michael Brown",
            "commenter_avatar": "/storage/avatars/michael.jpg",
            "user": {
                "id": 9,
                "name": "Michael Brown",
                "username": "mbrown",
                "avatar": "/storage/avatars/michael.jpg"
            },
            "article_id": 46,
            "article_title": "Getting Started with Flutter 3.x",
            "article": {
                "id": 46,
                "title": "Getting Started with Flutter 3.x",
                "slug": "getting-started-with-flutter-3x"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1,
        "has_more": false
    }
}
```
- **Error Responses**:
  - `403` (Reader token): `{"success": false, "message": "You must be registered as an author to moderate comments."}`

---

### POST `/api/v1/author/comments/{id}/approve`
Approves a comment on an author's post. Automatically increments the article's `comment_count`.
- **Auth**: Bearer Token
- **Role**: Owner of the commented article (`author`) or platform `admin`.
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Comment approved successfully.",
    "data": {
        "id": 128,
        "article_id": 46,
        "user_id": 9,
        "status": "approved"
    }
}
```
- **Error Responses**:
  - `403` (Another author attempts approval):
    ```json
    {
        "success": false,
        "message": "You can only moderate comments on your own articles."
    }
    ```
  - `404`: `{"success": false, "message": "Comment not found."}`

---

### DELETE `/api/v1/author/comments/{id}`
Deletes a comment (and all of its replies) on the author's post. Decrements `comment_count` if it was approved.
- **Auth**: Bearer Token
- **Role**: Owner of the commented article (`author`) or platform `admin`.
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Comment deleted successfully."
}
```
- **Error Responses**:
  - `403` (Another author attempts delete):
    ```json
    {
        "success": false,
        "message": "You can only delete comments on your own articles."
    }
    ```
  - `404`: `{"success": false, "message": "Comment not found."}`

---

## 10. Profile Management Endpoints

### GET `/api/v1/profile`
Returns detailed profile and performance metrics for the author.
- **Auth**: Bearer Token
- **Role**: Authenticated user
- **Success Response (200 OK)**:
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 2,
            "name": "Alex Author",
            "username": "alex-author",
            "email": "alex@example.com",
            "role": "author",
            "avatar": "/storage/avatars/user_2.jpg",
            "cover_image": "/storage/covers/user_2_cover.jpg",
            "bio": "Tech blogger & Flutter enthusiast",
            "tagline": "Coding the future",
            "specialty": "Mobile Development",
            "social": {
                "github": "https://github.com/alex",
                "twitter": "https://twitter.com/alex"
            }
        },
        "stats": {
            "total_articles": 15,
            "published_articles": 11,
            "drafts": 3,
            "total_views": 14200,
            "total_likes_received": 580,
            "bookmarks_count": 22,
            "followers_count": 94,
            "following_count": 18
        }
    }
}
```

---

### POST `/api/v1/profile/update`
Update profile information, avatar, and cover image.
- **Auth**: Bearer Token
- **Role**: Authenticated user
- **Content-Type**: `multipart/form-data` or `application/json`
- **Request Fields**:
  | Field | Type | Required | Rules & Description |
  |---|---|---|---|
  | `name` | string | No | Max 255 chars |
  | `username` | string | No | Unique, alpha_dash, max 50 chars |
  | `bio` | string | No | Max 1000 chars |
  | `tagline` | string | No | Max 255 chars |
  | `specialty` | string | No | Max 255 chars (e.g. `"Flutter Specialist"`) |
  | `avatar` | file | No | Image file (jpg, png, webp, max: 2048 KB) |
  | `cover_image` | file | No | Image file (jpg, png, webp, max: 4096 KB) |
  | `social` | object or json | No | Key-value pairs of social links |

- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Profile updated successfully.",
    "data": {
        "id": 2,
        "name": "Alex Author Updated",
        "username": "alex-author",
        "avatar": "/storage/avatars/new_avatar.png",
        "cover_image": "/storage/covers/new_cover.jpg",
        "bio": "Updated biography...",
        "specialty": "Flutter & Dart"
    }
}
```
- **Error Responses**:
  - `422`: `{"success": false, "errors": {"username": ["The username has already been taken."]}}`

---

### POST `/api/v1/profile/password`
- **Auth**: Bearer Token
- **Role**: Authenticated user
- **Request Fields** (`application/json`):
  | Field | Type | Required | Rules |
  |---|---|---|---|
  | `current_password` | string | **Yes** | Existing account password |
  | `password` | string | **Yes** | Min 8 chars |
  | `password_confirmation` | string | **Yes** | Must match `password` |

- **Success Response (200 OK)**:
```json
{
    "success": true,
    "message": "Password updated successfully."
}
```
- **Error Responses**:
  - `422`: `{"success": false, "message": "Current password does not match."}`
  - `422`: `{"success": false, "errors": {"password": ["The password confirmation does not match."]}}`

---

## 11. Local Development & Flutter Setup Guide

### 1. Starting Backend for Mobile Access
In terminal at backend root:
```bash
# 1. Create storage symlink so uploaded images are publicly served
php artisan storage:link

# 2. Run the dev server binding to 0.0.0.0 (allows emulator & local network connections)
php artisan serve --host=0.0.0.0 --port=8000
```

### 2. Base URL Configuration in Flutter (Dio / Http)
```dart
import 'dart:io';

class AppConfig {
  static String get baseUrl {
    if (Platform.isAndroid) {
      // Android Emulator maps host machine localhost to 10.0.2.2
      return 'http://10.0.2.2:8000/api/v1';
    } else if (Platform.isIOS) {
      // iOS Simulator shares localhost with host machine
      return 'http://localhost:8000/api/v1';
    } else {
      // Physical device over local Wi-Fi: use your computer's LAN IP
      return 'http://192.168.1.100:8000/api/v1';
    }
  }

  static String get storageBaseUrl {
    if (Platform.isAndroid) {
      return 'http://10.0.2.2:8000';
    } else if (Platform.isIOS) {
      return 'http://localhost:8000';
    } else {
      return 'http://192.168.1.100:8000';
    }
  }
}
```

### 3. Flutter Multipart Upload Example (Dio)
```dart
import 'package:dio/dio.dart';

Future<void> updateArticle({
  required int articleId,
  required String title,
  required String content,
  required String categorySlug,
  File? newCoverImage,
  required String authToken,
}) async {
  final dio = Dio();
  dio.options.headers['Authorization'] = 'Bearer $authToken';
  dio.options.headers['Accept'] = 'application/json';

  final Map<String, dynamic> formMap = {
    '_method': 'PUT', // Critical: allows multipart update via POST
    'title': title,
    'content': content,
    'category_slug': categorySlug,
  };

  if (newCoverImage != null) {
    formMap['image'] = await MultipartFile.fromFile(
      newCoverImage.path,
      filename: newCoverImage.path.split('/').last,
    );
  }

  final formData = FormData.fromMap(formMap);

  final response = await dio.post(
    '${AppConfig.baseUrl}/articles/$articleId',
    data: formData,
  );

  print('Updated: ${response.data}');
}
```
