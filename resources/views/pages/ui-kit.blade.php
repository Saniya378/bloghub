@extends('layouts.app')

@section('title', 'UI Kit & Design System — BlogHub')
@section('meta_description', 'Living style guide, design tokens, and reusable UI components for BlogHub.')

@section('content')
<section class="py-5" style="background-color: var(--color-bg);">
    <div class="container">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 pb-4 mb-5" style="border-bottom: 1px solid var(--color-border);">
            <div>
                <span class="bh-section-kicker">Design System &amp; Living Style Guide</span>
                <h1 class="bh-h1 mb-2">BlogHub UI Kit</h1>
                <p class="bh-body mb-0" style="color: var(--color-text-muted);">
                    Unified component library, design tokens, typography scale, and 8px spacing primitives.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn-bh btn-bh-accent" data-toggle="theme">
                    <i class="fa-solid fa-moon"></i> Toggle Light / Dark Mode
                </button>
            </div>
        </div>

        <!-- 1. Design Tokens: Color Palette -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Foundation</span>
                <h2 class="bh-section-title">Design Tokens: Color Palette</h2>
                <p class="bh-section-desc">Six primary brand tokens mapped to CSS custom properties with automatic dark-mode counterparts.</p>
            </div>

            <div class="row g-3">
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="bh-card p-3 text-center h-100">
                        <div style="height: 60px; background-color: #1F2A44; border-radius: var(--border-radius); margin-bottom: var(--space-2); border: 1px solid rgba(0,0,0,0.1);"></div>
                        <strong class="d-block" style="font-size: 0.9375rem;">Primary / Navy</strong>
                        <code class="bh-caption">#1F2A44</code>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="bh-card p-3 text-center h-100">
                        <div style="height: 60px; background-color: #C8461F; border-radius: var(--border-radius); margin-bottom: var(--space-2);"></div>
                        <strong class="d-block" style="font-size: 0.9375rem;">Accent / Coral</strong>
                        <code class="bh-caption">#C8461F</code>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="bh-card p-3 text-center h-100">
                        <div style="height: 60px; background-color: #3F5A45; border-radius: var(--border-radius); margin-bottom: var(--space-2);"></div>
                        <strong class="d-block" style="font-size: 0.9375rem;">Secondary / Moss</strong>
                        <code class="bh-caption">#3F5A45</code>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="bh-card p-3 text-center h-100">
                        <div style="height: 60px; background-color: #FBFAF7; border: 1px solid var(--color-border); border-radius: var(--border-radius); margin-bottom: var(--space-2);"></div>
                        <strong class="d-block" style="font-size: 0.9375rem;">Background</strong>
                        <code class="bh-caption">#FBFAF7</code>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="bh-card p-3 text-center h-100">
                        <div style="height: 60px; background-color: #14171F; border-radius: var(--border-radius); margin-bottom: var(--space-2);"></div>
                        <strong class="d-block" style="font-size: 0.9375rem;">Ink / Text</strong>
                        <code class="bh-caption">#14171F</code>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="bh-card p-3 text-center h-100">
                        <div style="height: 60px; background-color: #D8D5CC; border-radius: var(--border-radius); margin-bottom: var(--space-2);"></div>
                        <strong class="d-block" style="font-size: 0.9375rem;">Line / Border</strong>
                        <code class="bh-caption">#D8D5CC</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Typography Scale & Fonts -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Typography Hierarchy</span>
                <h2 class="bh-section-title">Type Scale &amp; Font Families</h2>
                <p class="bh-section-desc">Fraunces (Headings), Inter (Body UI), and IBM Plex Mono (Labels, Meta &amp; Dates).</p>
            </div>

            <div class="bh-card p-4">
                <div class="d-flex flex-column gap-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-baseline pb-3" style="border-bottom: 1px solid var(--color-border-subtle);">
                        <span class="bh-h1 mb-0">H1 Display Headline — 40px (2.5rem)</span>
                        <code class="bh-caption">Fraunces 700 / 40px / 1.25 line-height</code>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-baseline pb-3" style="border-bottom: 1px solid var(--color-border-subtle);">
                        <span class="bh-h2 mb-0">H2 Section Heading — 28px (1.75rem)</span>
                        <code class="bh-caption">Fraunces 700 / 28px / 1.25 line-height</code>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-baseline pb-3" style="border-bottom: 1px solid var(--color-border-subtle);">
                        <span class="bh-h3 mb-0">H3 Sub-section &amp; Card Title — 20px (1.25rem)</span>
                        <code class="bh-caption">Fraunces 700 / 20px / 1.25 line-height</code>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-baseline pb-3" style="border-bottom: 1px solid var(--color-border-subtle);">
                        <span class="bh-body mb-0">Body Text — 16px (1.0rem) standard prose and interface paragraphs with optimal reading contrast.</span>
                        <code class="bh-caption">Inter 400 / 16px / 1.6 line-height</code>
                    </div>
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-baseline">
                        <span class="bh-caption mb-0">CAPTION / META — 13px (0.8125rem) for publishing dates, reading times, tags, and footers.</span>
                        <code class="bh-caption">IBM Plex Mono 500 / 13px / 1.4 line-height</code>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Spacing Scale Utilities (8px Grid) -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Layout Primitives</span>
                <h2 class="bh-section-title">8px Spacing Grid Utilities</h2>
                <p class="bh-section-desc">Strict multiples of 4px and 8px across margins, paddings, and flex/grid gaps.</p>
            </div>

            <div class="bh-card p-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-2 col-4">
                        <code class="bh-caption">--space-1 (4px)</code>
                        <div style="height: 16px; width: 4px; background-color: var(--color-accent); border-radius: 2px;"></div>
                    </div>
                    <div class="col-md-2 col-4">
                        <code class="bh-caption">--space-2 (8px)</code>
                        <div style="height: 16px; width: 8px; background-color: var(--color-accent); border-radius: 2px;"></div>
                    </div>
                    <div class="col-md-2 col-4">
                        <code class="bh-caption">--space-4 (16px)</code>
                        <div style="height: 16px; width: 16px; background-color: var(--color-accent); border-radius: 2px;"></div>
                    </div>
                    <div class="col-md-2 col-4">
                        <code class="bh-caption">--space-6 (24px)</code>
                        <div style="height: 16px; width: 24px; background-color: var(--color-accent); border-radius: 2px;"></div>
                    </div>
                    <div class="col-md-2 col-4">
                        <code class="bh-caption">--space-8 (32px)</code>
                        <div style="height: 16px; width: 32px; background-color: var(--color-accent); border-radius: 2px;"></div>
                    </div>
                    <div class="col-md-2 col-4">
                        <code class="bh-caption">--space-12 (48px)</code>
                        <div style="height: 16px; width: 48px; background-color: var(--color-accent); border-radius: 2px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Buttons (All 44px Uniform Height) -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Interactive Elements</span>
                <h2 class="bh-section-title">Buttons (Uniform 44px Height)</h2>
                <p class="bh-section-desc">Primary, Secondary, Accent, Ghost, Icon-Only, and Disabled states. All buttons match the exact height token (44px).</p>
            </div>

            <div class="bh-card p-4">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <button type="button" class="btn-bh btn-bh-primary">
                        <i class="fa-solid fa-pen-nib"></i> Primary Button
                    </button>
                    <button type="button" class="btn-bh btn-bh-secondary">
                        <i class="fa-solid fa-bookmark"></i> Secondary Button
                    </button>
                    <button type="button" class="btn-bh btn-bh-accent">
                        <i class="fa-solid fa-fire"></i> Accent Button
                    </button>
                    <button type="button" class="btn-bh btn-bh-ghost">
                        <i class="fa-solid fa-share-nodes"></i> Ghost Button
                    </button>
                    <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" aria-label="Bookmark article" title="Bookmark">
                        <i class="fa-regular fa-bookmark"></i>
                    </button>
                    <button type="button" class="btn-bh btn-bh-primary btn-bh-icon" aria-label="Like article" title="Like">
                        <i class="fa-solid fa-heart"></i>
                    </button>
                    <button type="button" class="btn-bh btn-bh-primary" disabled>
                        Disabled Button
                    </button>
                </div>
            </div>
        </div>

        <!-- 5. Badges & Tags -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Micro Components</span>
                <h2 class="bh-section-title">Badges &amp; Tags</h2>
                <p class="bh-section-desc">Category badges and status indicators using IBM Plex Mono.</p>
            </div>

            <div class="bh-card p-4">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span class="bh-badge bh-badge-accent">
                        <i class="fa-solid fa-bolt"></i> Technology
                    </span>
                    <span class="bh-badge bh-badge-primary">
                        <i class="fa-solid fa-palette"></i> Design &amp; UI
                    </span>
                    <span class="bh-badge bh-badge-secondary">
                        <i class="fa-solid fa-leaf"></i> Sustainability
                    </span>
                    <span class="bh-badge bh-badge-outline">
                        <i class="fa-solid fa-tag"></i> Editorial
                    </span>
                    <span class="bh-badge bh-badge-outline">
                        5 min read
                    </span>
                </div>
            </div>
        </div>

        <!-- 6. Alerts -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Feedback</span>
                <h2 class="bh-section-title">Alerts</h2>
                <p class="bh-section-desc">Contextual messaging with semantic icons and distinct status colors.</p>
            </div>

            <div class="d-flex flex-column gap-3">
                <div class="bh-alert bh-alert-info" role="alert">
                    <i class="fa-solid fa-circle-info bh-alert-icon"></i>
                    <div class="bh-alert-content">
                        <div class="bh-alert-title">Information Notice</div>
                        Your draft has been saved locally. You can preview your article before submitting for review.
                    </div>
                </div>

                <div class="bh-alert bh-alert-success" role="alert">
                    <i class="fa-solid fa-circle-check bh-alert-icon"></i>
                    <div class="bh-alert-content">
                        <div class="bh-alert-title">Success State</div>
                        Thank you for subscribing! Your confirmation email is on its way.
                    </div>
                </div>

                <div class="bh-alert bh-alert-warning" role="alert">
                    <i class="fa-solid fa-triangle-exclamation bh-alert-icon"></i>
                    <div class="bh-alert-content">
                        <div class="bh-alert-title">Attention Required</div>
                        Please complete your author profile bio to qualify for featured spotlights.
                    </div>
                </div>

                <div class="bh-alert bh-alert-danger" role="alert">
                    <i class="fa-solid fa-circle-exclamation bh-alert-icon"></i>
                    <div class="bh-alert-content">
                        <div class="bh-alert-title">Validation Error</div>
                        Unable to process request. Please ensure all required fields are correctly completed.
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Breadcrumbs -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Navigation</span>
                <h2 class="bh-section-title">Breadcrumbs</h2>
                <p class="bh-section-desc">Semantic trail showing current hierarchy.</p>
            </div>

            <div class="bh-card p-4">
                <nav aria-label="Breadcrumb example">
                    <ol class="bh-breadcrumb mb-0">
                        <li class="bh-breadcrumb-item">
                            <a href="{{ url('/') }}"><i class="fa-solid fa-house me-1"></i>Home</a>
                            <span class="bh-breadcrumb-separator">/</span>
                        </li>
                        <li class="bh-breadcrumb-item">
                            <a href="{{ url('/blogs') }}">Blogs</a>
                            <span class="bh-breadcrumb-separator">/</span>
                        </li>
                        <li class="bh-breadcrumb-item">
                            <a href="{{ url('/categories') }}">Technology</a>
                            <span class="bh-breadcrumb-separator">/</span>
                        </li>
                        <li class="bh-breadcrumb-item active" aria-current="page">
                            Building Resilient Web Applications in 2026
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- 8. Cards -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Content Containers</span>
                <h2 class="bh-section-title">Cards</h2>
                <p class="bh-section-desc">Sample blog card component with image hover zoom, tags, title, excerpt, and footer actions.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <article class="bh-card bh-card-hover h-100">
                        <div class="bh-card-img-wrap">
                            <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=800&q=80" alt="Laptop displaying code in a modern dark editorial workspace">
                        </div>
                        <div class="bh-card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="bh-badge bh-badge-accent">Engineering</span>
                                <span class="bh-caption">6 min read</span>
                            </div>
                            <h3 class="bh-card-title">
                                <a href="#">The Architectural Elegance of Component-First Styling</a>
                            </h3>
                            <p class="bh-card-text">
                                Exploring how modern typography, strict design tokens, and modular CSS establish cohesive digital experiences.
                            </p>
                            <div class="bh-card-meta mb-0 pt-3" style="border-top: 1px solid var(--color-border-subtle);">
                                <span class="fw-semibold" style="color: var(--color-primary);">Elena Rostova</span>
                                <span>&bull;</span>
                                <span>Oct 14, 2026</span>
                            </div>
                        </div>
                        <div class="bh-card-footer">
                            <a href="#" class="btn-bh btn-bh-ghost" style="padding: 0 var(--space-3); height: 36px;">Read Story &rarr;</a>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" style="width: 36px; height: 36px;" aria-label="Bookmark this article">
                                    <i class="fa-regular fa-bookmark"></i>
                                </button>
                                <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" style="width: 36px; height: 36px;" aria-label="Like this article">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="col-md-6 col-lg-4">
                    <article class="bh-card bh-card-hover h-100">
                        <div class="bh-card-img-wrap">
                            <img src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?auto=format&fit=crop&w=800&q=80" alt="Minimalist editorial workspace with notepad and ceramic coffee mug">
                        </div>
                        <div class="bh-card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="bh-badge bh-badge-primary">Design</span>
                                <span class="bh-caption">4 min read</span>
                            </div>
                            <h3 class="bh-card-title">
                                <a href="#">Typography as Identity: Editorial Layouts in Modern Web</a>
                            </h3>
                            <p class="bh-card-text">
                                Why serif headlines paired with crisp geometric sans-serifs create an authoritative and memorable visual tone.
                            </p>
                            <div class="bh-card-meta mb-0 pt-3" style="border-top: 1px solid var(--color-border-subtle);">
                                <span class="fw-semibold" style="color: var(--color-primary);">Marcus Chen</span>
                                <span>&bull;</span>
                                <span>Oct 12, 2026</span>
                            </div>
                        </div>
                        <div class="bh-card-footer">
                            <a href="#" class="btn-bh btn-bh-ghost" style="padding: 0 var(--space-3); height: 36px;">Read Story &rarr;</a>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" style="width: 36px; height: 36px;" aria-label="Bookmark this article">
                                    <i class="fa-regular fa-bookmark"></i>
                                </button>
                                <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" style="width: 36px; height: 36px;" aria-label="Like this article">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="col-md-6 col-lg-4">
                    <article class="bh-card bh-card-hover h-100">
                        <div class="bh-card-img-wrap">
                            <img src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=800&q=80" alt="Abstract satellite view of glowing network grid">
                        </div>
                        <div class="bh-card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="bh-badge bh-badge-secondary">Philosophy</span>
                                <span class="bh-caption">8 min read</span>
                            </div>
                            <h3 class="bh-card-title">
                                <a href="#">The Quiet Revolution of Digital Publishing Platforms</a>
                            </h3>
                            <p class="bh-card-text">
                                Navigating information overload through curation, slow reading habits, and disciplined typography.
                            </p>
                            <div class="bh-card-meta mb-0 pt-3" style="border-top: 1px solid var(--color-border-subtle);">
                                <span class="fw-semibold" style="color: var(--color-primary);">Sarah Jenkins</span>
                                <span>&bull;</span>
                                <span>Oct 08, 2026</span>
                            </div>
                        </div>
                        <div class="bh-card-footer">
                            <a href="#" class="btn-bh btn-bh-ghost" style="padding: 0 var(--space-3); height: 36px;">Read Story &rarr;</a>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" style="width: 36px; height: 36px;" aria-label="Bookmark this article">
                                    <i class="fa-regular fa-bookmark"></i>
                                </button>
                                <button type="button" class="btn-bh btn-bh-ghost btn-bh-icon" style="width: 36px; height: 36px;" aria-label="Like this article">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <!-- 9. Form Inputs & Controls -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Data Entry</span>
                <h2 class="bh-section-title">Form Controls &amp; Validation States</h2>
                <p class="bh-section-desc">Strict 44px input height with explicit labels, placeholders, focus halos, and error messaging.</p>
            </div>

            <div class="bh-card p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="bh-form-group">
                            <label for="exampleInputText" class="bh-form-label">
                                Full Name <span style="color: var(--color-accent);">*</span>
                            </label>
                            <input type="text" id="exampleInputText" class="bh-form-control" placeholder="e.g. Maya Angelou">
                            <span class="bh-form-hint">Displayed publicly on authored articles.</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bh-form-group">
                            <label for="exampleSelect" class="bh-form-label">
                                Primary Category
                            </label>
                            <select id="exampleSelect" class="bh-form-select">
                                <option value="tech">Technology &amp; Engineering</option>
                                <option value="design">Design &amp; Creative</option>
                                <option value="culture">Culture &amp; Society</option>
                                <option value="business">Business &amp; Strategy</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bh-form-group">
                            <label for="exampleInputInvalid" class="bh-form-label">
                                Email Address (Invalid State Example)
                            </label>
                            <input type="email" id="exampleInputInvalid" class="bh-form-control is-invalid" value="invalid-email@" aria-invalid="true" aria-describedby="emailFeedback">
                            <span id="emailFeedback" class="bh-form-feedback">Please enter a valid email address.</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bh-form-group">
                            <label for="exampleTextarea" class="bh-form-label">Author Bio (Textarea)</label>
                            <textarea id="exampleTextarea" class="bh-form-control" placeholder="Tell readers about your background and interests..."></textarea>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="bh-form-check">
                            <input type="checkbox" id="exampleCheckbox" checked>
                            <span class="bh-form-check-label">Send me notifications when readers comment on my stories</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- 10. Pagination -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Navigation</span>
                <h2 class="bh-section-title">Pagination</h2>
                <p class="bh-section-desc">Accessible page controls with 44px clickable touch targets.</p>
            </div>

            <div class="bh-card p-4">
                <nav aria-label="Blog pagination">
                    <ul class="bh-pagination">
                        <li class="bh-pagination-item disabled">
                            <a class="bh-pagination-link" href="#" aria-label="Previous Page" tabindex="-1">
                                <i class="fa-solid fa-chevron-left"></i>
                            </a>
                        </li>
                        <li class="bh-pagination-item active">
                            <a class="bh-pagination-link" href="#" aria-current="page">1</a>
                        </li>
                        <li class="bh-pagination-item">
                            <a class="bh-pagination-link" href="#">2</a>
                        </li>
                        <li class="bh-pagination-item">
                            <a class="bh-pagination-link" href="#">3</a>
                        </li>
                        <li class="bh-pagination-item">
                            <a class="bh-pagination-link" href="#">4</a>
                        </li>
                        <li class="bh-pagination-item">
                            <a class="bh-pagination-link" href="#" aria-label="Next Page">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- 11. Toast Notifications, Tooltip, Spinner, & Progress Bar -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Feedback &amp; Status</span>
                <h2 class="bh-section-title">Toast, Tooltip, Spinner &amp; Progress Bar</h2>
                <p class="bh-section-desc">Interactive state indicators for async actions and reader feedback.</p>
            </div>

            <div class="row g-4">
                <!-- Toast Demonstration -->
                <div class="col-lg-6">
                    <div class="bh-card p-4 h-100">
                        <h3 class="bh-h3 mb-3">Toast Notifications</h3>
                        <p class="bh-body">Click the buttons below to trigger client-side toast notifications:</p>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <button type="button" class="btn-bh btn-bh-accent" onclick="showToast('Story Saved', 'Article has been added to your reading list.', 'fa-solid fa-bookmark text-accent')">
                                <i class="fa-solid fa-bookmark"></i> Trigger Saved Toast
                            </button>
                            <button type="button" class="btn-bh btn-bh-secondary" onclick="showToast('Link Copied', 'Article URL copied to your clipboard.', 'fa-solid fa-link text-success')">
                                <i class="fa-solid fa-link"></i> Trigger Copy Toast
                            </button>
                        </div>

                        <div class="bh-caption mb-2">Static Toast Preview:</div>
                        <div class="bh-toast position-relative">
                            <div class="bh-toast-icon">
                                <i class="fa-solid fa-circle-check text-success"></i>
                            </div>
                            <div class="bh-toast-body">
                                <div class="bh-toast-title">Newsletter Subscribed</div>
                                <p class="bh-toast-message">You will receive the next edition this Friday.</p>
                            </div>
                            <button type="button" class="bh-toast-close" aria-label="Close notification">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tooltips, Spinners & Progress -->
                <div class="col-lg-6">
                    <div class="bh-card p-4 h-100">
                        <h3 class="bh-h3 mb-3">Tooltips, Spinners &amp; Progress Bars</h3>
                        
                        <!-- Tooltip Demo -->
                        <div class="mb-4">
                            <span class="bh-caption d-block mb-2">Hover or Focus Tooltip:</span>
                            <div class="bh-tooltip-wrapper">
                                <button type="button" class="btn-bh btn-bh-ghost">
                                    <i class="fa-solid fa-circle-question"></i> Hover Over Me
                                </button>
                                <span class="bh-tooltip" role="tooltip">Verified Author Badge</span>
                            </div>
                        </div>

                        <!-- Spinners Demo -->
                        <div class="mb-4">
                            <span class="bh-caption d-block mb-2">Spinners (sm, default, lg):</span>
                            <div class="d-flex align-items-center gap-3">
                                <span class="bh-spinner bh-spinner-sm" aria-label="Loading small"></span>
                                <span class="bh-spinner" aria-label="Loading medium"></span>
                                <span class="bh-spinner bh-spinner-lg" aria-label="Loading large"></span>
                                <span class="bh-spinner bh-spinner-secondary" aria-label="Loading secondary"></span>
                                <span class="bh-spinner bh-spinner-primary" aria-label="Loading primary"></span>
                            </div>
                        </div>

                        <!-- Progress Bar Demo -->
                        <div>
                            <span class="bh-caption d-block mb-2">Progress Bars (Accent, Secondary, Primary):</span>
                            <div class="d-flex flex-column gap-3">
                                <div>
                                    <div class="d-flex justify-content-between bh-caption mb-1">
                                        <span>Reading Progress</span>
                                        <span>65%</span>
                                    </div>
                                    <div class="bh-progress">
                                        <div class="bh-progress-bar" style="width: 65%;"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-between bh-caption mb-1">
                                        <span>Author Level</span>
                                        <span>40%</span>
                                    </div>
                                    <div class="bh-progress">
                                        <div class="bh-progress-bar bh-progress-bar-secondary" style="width: 40%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 12. Section Headings Showcase -->
        <div class="mb-5">
            <div class="bh-section-header with-line">
                <span class="bh-section-kicker">Layout Showcase</span>
                <h2 class="bh-section-title">Section Headings Hierarchy</h2>
                <p class="bh-section-desc">Consistent headers across Home, Blogs, and Directory pages.</p>
            </div>

            <div class="bh-card p-4">
                <div class="bh-section-header mb-0">
                    <span class="bh-section-kicker">Curated Weekly</span>
                    <h2 class="bh-section-title">Featured Stories &amp; Deep Analyses</h2>
                    <p class="bh-section-desc">
                        Our editors select the most thought-provoking articles across technology, culture, and business every morning.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
