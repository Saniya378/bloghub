@extends('layouts.app')

@section('title', 'BlogHub — Thoughtful Perspectives on Tech, Design & Culture')
@section('meta_description', 'Discover long-form essays, tech deep dives, and cultural commentary written by independent authors on BlogHub.')
@include('partials.navbar')

@section('content')
<!-- Hero Section -->
<section class="bh-hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="bh-badge bh-badge-primary mb-3">Edition 2026</span>
                <h1 class="bh-h1 mb-4" style="font-size: clamp(2rem, 4.5vw, 3rem);">Thoughtful Perspectives on Technology, Design &amp; Culture.</h1>
                <p class="bh-body mb-5" style="font-size: 1.125rem; color: var(--color-text-muted);">Where independent writers, systems engineers, and creative theorists publish in-depth essays, architectural breakdowns, and unhurried reflections.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ url('/blogs') }}" class="btn-bh btn-bh-primary">Explore Blogs</a>
                    @auth
                        @if(auth()->user()->role === 'reader')
                        <form method="POST" action="{{ url('/become-author') }}">
                            @csrf
                            <button type="submit" class="btn-bh btn-bh-ghost">Become an Author</button>
                        </form>
                        @endif
                    @else
                    <a href="{{ url('/register') }}" class="btn-bh btn-bh-ghost">Become an Author</a>
                    @endauth
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bh-hero-img-frame">
                    <img src="https://picsum.photos/seed/bloghub-hero/800/600" alt="Editorial workspace" width="800" height="600" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Articles -->
<section class="py-5 bh-reveal" style="background-color: var(--color-surface-subtle);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
            <div>
                <span class="bh-section-kicker">Curator's Spotlight</span>
                <h2 class="bh-section-title mb-0">Featured Articles</h2>
            </div>
            <a href="{{ url('/blogs') }}" class="btn-bh btn-bh-ghost">All Articles</a>
        </div>
        <div class="row g-4">
            @if($featured->isNotEmpty())
                <div class="col-lg-7">
                    @include('components.article-card', ['article' => $featured->first(), 'layout' => 'featured'])
                </div>
                <div class="col-lg-5 d-flex flex-column gap-4">
                    @foreach($featured->skip(1) as $article)
                        @include('components.article-card', ['article' => $article, 'layout' => 'compact'])
                    @endforeach
                </div>
            @else
                <div class="col-12 text-center py-5" style="color: var(--color-text-muted);">No featured articles yet.</div>
            @endif
        </div>
    </div>
</section>

<!-- Trending Posts -->
<section class="py-5 bh-reveal">
    <div class="container">
        <div class="mb-5">
            <span class="bh-section-kicker">Reader Favorites</span>
            <h2 class="bh-section-title">Trending Now</h2>
        </div>
        <div class="row g-3">
            @foreach($trending as $index => $article)
            <div class="col-md-6">
                <a href="{{ url('/blogs/' . $article->slug) }}" class="bh-trending-item">
                    <span class="bh-trending-rank">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="bh-trending-content">
                        <h3 class="bh-trending-title">{{ $article->title }}</h3>
                        <div class="bh-trending-meta">
                            <span>{{ $article->user->name }}</span>
                            <span>&bull;</span>
                            <span>{{ $article->reading_time }}</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Latest Posts -->
<section class="py-5 bh-reveal" style="background-color: var(--color-surface-subtle);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
            <div>
                <span class="bh-section-kicker">Chronological Stream</span>
                <h2 class="bh-section-title mb-0">Latest Publications</h2>
            </div>
            <a href="{{ url('/blogs') }}" class="btn-bh btn-bh-ghost">View All</a>
        </div>
        <div class="row g-4">
            @foreach($latest as $article)
            <div class="col-md-6 col-lg-4">
                @include('components.article-card', ['article' => $article])
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Popular Categories -->
<section class="py-5 bh-reveal">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
            <div>
                <span class="bh-section-kicker">Subject Exploration</span>
                <h2 class="bh-section-title mb-0">Popular Categories</h2>
            </div>
            <a href="{{ url('/categories') }}" class="btn-bh btn-bh-ghost">Browse All</a>
        </div>
        <div class="row g-3">
            @php
            $categoryIcons = [
                'technology' => 'fa-microchip', 'education' => 'fa-graduation-cap', 'travel' => 'fa-compass',
                'food' => 'fa-utensils', 'business' => 'fa-chart-line', 'sports' => 'fa-medal',
                'health' => 'fa-heart-pulse', 'lifestyle' => 'fa-couch', 'finance' => 'fa-wallet',
                'science' => 'fa-atom', 'art' => 'fa-palette', 'culture' => 'fa-landmark',
            ];
            @endphp
            @foreach($categories as $category => $count)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ url('/blogs?category=' . $category) }}" class="bh-category-pill">
                    <div class="bh-category-icon-box">
                        <i class="fa-solid {{ $categoryIcons[$category] ?? 'fa-folder' }}"></i>
                    </div>
                    <div>
                        <div class="bh-category-title">{{ ucfirst($category) }}</div>
                        <span class="bh-category-count">{{ $count }} articles</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured Authors -->
<section class="py-5 bh-reveal" style="background-color: var(--color-surface-subtle);">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
            <div>
                <span class="bh-section-kicker">Leading Voices</span>
                <h2 class="bh-section-title mb-0">Featured Authors</h2>
            </div>
            <a href="{{ url('/authors') }}" class="btn-bh btn-bh-ghost">All Authors</a>
        </div>
        <div class="row g-4">
            @foreach($authors as $author)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ route('authors.show', $author->username) }}" class="text-decoration-none h-100">
                    <div class="bh-author-card">
                        <img src="{{ $author->avatar }}" alt="{{ $author->name }}" class="bh-author-avatar" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/avatar-placeholder.svg') }}'">
                        <h3 class="bh-author-name">{{ $author->name }}</h3>
                        <p class="bh-author-tagline">{{ $author->tagline }}</p>
                        <div class="bh-author-meta">
                            <span class="fw-semibold" style="color: var(--color-primary);">{{ $author->articles_count }} articles</span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Statistics Band -->
<section class="bh-stats-band bh-reveal">
    <div class="container">
        <div class="row text-center">
            <div class="col-6 col-md-3">
                <div class="bh-stat-card">
                    <div class="bh-stat-number" data-counter-target="{{ $stats['articles'] }}">0</div>
                    <div class="bh-stat-label">Published Essays</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="bh-stat-card">
                    <div class="bh-stat-number" data-counter-target="{{ $stats['authors'] }}">0</div>
                    <div class="bh-stat-label">Authors</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="bh-stat-card">
                    <div class="bh-stat-number" data-counter-target="{{ $stats['categories'] }}">0</div>
                    <div class="bh-stat-label">Knowledge Fields</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="bh-stat-card">
                    <div class="bh-stat-number" data-counter-target="{{ $stats['readers'] }}">0</div>
                    <div class="bh-stat-label">Registered Readers</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Carousel -->
@if($testimonials->isNotEmpty())
<section class="bh-testimonials-section bh-reveal">
    <div class="container">
        <div class="text-center mb-5">
            <span class="bh-section-kicker">Voices of the Community</span>
            <h2 class="bh-section-title">What Readers Say</h2>
        </div>
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($testimonials as $testimonial)
                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                    <div class="bh-testimonial-card">
                        <i class="fa-solid fa-quote-left bh-testimonial-quote-icon"></i>
                        <p class="bh-testimonial-quote">&ldquo;{{ $testimonial->quote }}&rdquo;</p>
                        <div class="bh-testimonial-author">
                            <img src="{{ $testimonial->photo }}" alt="{{ $testimonial->name }}" class="bh-testimonial-avatar" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/avatar-placeholder.svg') }}'">
                            <div class="bh-testimonial-name">{{ $testimonial->name }}</div>
                            <div class="bh-rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $testimonial->rating ? 'fa-solid fa-star' : 'fa-regular fa-star' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="bh-carousel-controls">
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <i class="fa-solid fa-chevron-left"></i>
                    <span class="visually-hidden">Previous</span>
                </button>
                <div class="carousel-indicators bh-carousel-indicators">
                    @foreach($testimonials as $testimonial)
                    <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}" aria-label="Testimonial {{ $loop->index + 1 }}"></button>
                    @endforeach
                </div>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <i class="fa-solid fa-chevron-right"></i>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Newsletter -->
<section class="bh-newsletter-section">
    <div class="container">
        <div class="bh-newsletter-box">
            <div class="bh-newsletter-inner">
                <span class="bh-section-kicker">The Sunday Dispatch</span>
                <h2 class="bh-section-title mb-3">Curated Long-Form Thought, Delivered Weekly.</h2>
                <p class="bh-section-desc mx-auto mb-0">We synthesize deep technology essays, cultural reflections, and craft insights without algorithmic noise.</p>
                <form id="newsletterForm" class="bh-newsletter-form">
                    @csrf
                    <div class="bh-newsletter-input-group">
                        <input type="email" name="email" class="bh-form-control" placeholder="Enter your email" required>
                        <button type="submit" class="btn-bh btn-bh-primary">
                            <span class="spinner-border spinner-border-sm d-none" id="newsletterSpinner"></span>
                            <span id="newsletterBtnText">Subscribe</span>
                        </button>
                    </div>
                </form>
                <div id="newsletterFeedback" class="mt-3"></div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Newsletter form
    const form = document.getElementById('newsletterForm');
    const spinner = document.getElementById('newsletterSpinner');
    const btnText = document.getElementById('newsletterBtnText');
    const feedback = document.getElementById('newsletterFeedback');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const email = form.email.value.trim();
        if (!email) {
            feedback.className = 'alert alert-danger';
            feedback.textContent = 'Please enter your email.';
            return;
        }
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Subscribing...';
        try {
            const response = await fetch('{{ url("/newsletter/subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email })
            });
            const data = await response.json();
            feedback.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
            feedback.textContent = data.message;
            if (data.success) {
                form.reset();
            }
        } catch (err) {
            feedback.className = 'alert alert-danger';
            feedback.textContent = 'Something went wrong. Please try again.';
        } finally {
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Subscribe';
        }
    });
});
</script>
@endpush
