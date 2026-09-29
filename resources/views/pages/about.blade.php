@extends('layouts.app')

@section('title', 'About — The BlogHub Story')
@section('meta_description', 'Learn about BlogHub — an editorial publishing platform built by independent writers, systems engineers, and creative theorists.')

@section('content')
<!-- Hero -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <span class="text-primary fw-semibold">Our Story</span>
                <h1 class="display-4 fw-bold mb-4">Publishing as a Craft, Not a Content Factory</h1>
                <p class="lead text-muted">BlogHub began in 2019 as a shared document between three engineers who were tired of watching thoughtful essays get buried under algorithmic noise.</p>
            </div>
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="text-primary fw-semibold">The Beginning</span>
                <h2 class="h3 fw-bold mb-4">From Frustration to Foundation</h2>
                <p class="text-muted">In early 2019, our founding team — Elena Rostova, Marcus Chen, and Dr. Sarah Jenkins — were working at different tech companies across Berlin, Toronto, and Melbourne. We kept running into the same problem: the best writing we read was never on the platforms designed for it.</p>
                <p class="text-muted">Long-form essays were being squeezed into character-limited feeds, buried by engagement algorithms, or locked behind paywalls that favored volume over depth. So we started a shared document. Every week, one of us would publish an essay — no ads, no infinite scroll, no recommendation engine deciding what deserved attention.</p>
                <p class="text-muted">That document became our prototype. By mid-2020, we had rebuilt it as a proper platform and opened it to a small group of invited writers.</p>
            </div>
            <div class="col-lg-6">
                <img src="https://picsum.photos/seed/bloghub-office/800/600" alt="Editorial workspace" class="img-fluid rounded-4 shadow" width="800" height="600">
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card h-100 p-4">
                    <i class="fa-solid fa-bullseye fa-2x text-primary mb-3"></i>
                    <h3 class="h4 fw-bold">Our Mission</h3>
                    <p class="text-muted">To provide a publishing home where independent writers can produce deep, unhurried work — and where readers can find substance without wading through noise. We believe the internet deserves better than engagement-optimized content farms, and that writers deserve a platform that respects their craft.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100 p-4">
                    <i class="fa-solid fa-eye fa-2x text-primary mb-3"></i>
                    <h3 class="h4 fw-bold">Our Vision</h3>
                    <p class="text-muted">A web where thoughtful writing thrives alongside — not beneath — the attention economy. We envision BlogHub as a sustainable ecosystem: writers earn fair compensation, readers get genuine insight, and the platform grows through quality rather than scale.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Strip -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold">The People</span>
            <h2 class="h3 fw-bold">Meet the Team</h2>
            <p class="text-muted">A small, distributed group of editors, engineers, and designers who believe in the written word.</p>
        </div>
        <div class="row g-4">
            @foreach($team as $member)
            <div class="col-6 col-md-3">
                <a href="{{ url('/authors/' . $member->username) }}" class="text-decoration-none">
                    <div class="text-center">
                        <img src="{{ $member->avatar }}" alt="{{ $member->name }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
                        <h3 class="h6 fw-semibold text-dark mb-1">{{ $member->name }}</h3>
                        <p class="text-muted small">{{ $member->specialty }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Achievements -->
<section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="display-5 fw-bold text-primary" data-counter="{{ $stats['articles'] }}">0</div>
                <div class="text-muted">Published Essays</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-5 fw-bold text-primary" data-counter="{{ $stats['authors'] }}">0</div>
                <div class="text-muted">Staff Authors</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-5 fw-bold text-primary" data-counter="{{ $stats['categories'] }}">0</div>
                <div class="text-muted">Knowledge Fields</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-5 fw-bold text-primary" data-counter="{{ $stats['readers'] }}">0</div>
                <div class="text-muted">Monthly Readers</div>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold">What We Stand For</span>
            <h2 class="h3 fw-bold">Our Editorial Principles</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 text-center">
                    <i class="fa-solid fa-feather fa-2x text-primary mb-3"></i>
                    <h3 class="h5 fw-bold">Depth Over Speed</h3>
                    <p class="text-muted small">We publish when an essay is ready, not when a trending topic demands it.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 text-center">
                    <i class="fa-solid fa-user-pen fa-2x text-primary mb-3"></i>
                    <h3 class="h5 fw-bold">Writer-First</h3>
                    <p class="text-muted small">Our authors retain full rights to their work and earn 70% of all revenue.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 text-center">
                    <i class="fa-solid fa-book-open fa-2x text-primary mb-3"></i>
                    <h3 class="h5 fw-bold">Reader Respect</h3>
                    <p class="text-muted small">No dark patterns, no infinite scroll, no engagement tricks.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 p-4 text-center">
                    <i class="fa-solid fa-shield-halved fa-2x text-primary mb-3"></i>
                    <h3 class="h5 fw-bold">Independence</h3>
                    <p class="text-muted small">We are bootstrapped and reader-supported. No venture capital pressure.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('[data-counter]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const end = parseInt(target.dataset.counter);
                const duration = 1800;
                const start = performance.now();
                const update = (now) => {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                    target.textContent = Math.floor(ease * end).toLocaleString();
                    if (progress < 1) requestAnimationFrame(update);
                };
                requestAnimationFrame(update);
                observer.unobserve(target);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(el => observer.observe(el));
});
</script>
@endpush
