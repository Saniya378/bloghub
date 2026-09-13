@extends('frontend.layouts.app')
@section('title')
 BlogHub | Homepage
@endsection
@section('content')
@include('frontend.components.navbar')

    <!-- ===== HERO SECTION ===== -->
    <section class="bg-dark text-light hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold">Discover Ideas.<br>Read. Learn. Grow.</h1>
            <p class="text-secondary fs-5 mt-3">
                Explore insightful articles on technology, lifestyle, business, travel and more.
            </p>
            <a href="{{ url('/categories') }}" class="btn btn-primary btn-lg me-2">Explore Articles</a>
            <a href="{{ url('/about') }}" class="btn btn-outline-light btn-lg">Learn More</a>
        </div>
    </section>

    <!-- ===== TRENDING POSTS ===== -->
    <section class="container py-5 my-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold">Trending Posts</h3>
            <a href="{{ url('/trending') }}" class="text-primary text-decoration-none">View All &rarr;</a>
        </div>

        <div class="row g-4">

            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm position-relative">
                    <a href="{{ url('/blog/future-of-ai') }}" class="stretched-link"></a>
                    <span class="badge bg-primary position-absolute m-2">Technology</span>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVTC-sGcFxRBrTzO-WIiUVUCpS3egZD3PBnug8tz5LzA&s=10" class="card-img-top" alt="Article image">
                    <div class="card-body">
                        <h5 class="card-title">The Future of Artificial Intelligence</h5>
                        <p class="card-text text-secondary small">May 16, 2025 &bull; 5 min read</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm position-relative">
                    <a href="{{ url('/blog/maldives-guide') }}" class="stretched-link"></a>
                    <span class="badge bg-success position-absolute m-2">Travel</span>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTXLHedoXnRsGLmryCFmkRr67p6DKDhIupswrSYmaBIvg&s=10" class="card-img-top" alt="Article image">
                    <div class="card-body">
                        <h5 class="card-title">Exploring the Maldives: A Guide</h5>
                        <p class="card-text text-secondary small">May 15, 2025 &bull; 6 min read</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm position-relative">
                    <a href="{{ url('/blog/healthy-recipes') }}" class="stretched-link"></a>
                    <span class="badge bg-warning text-dark position-absolute m-2">Food</span>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsU55EnkZObOdYQXWuOqSaKVJqhsqSYja4qEfgK3x-cg&s=10" class="card-img-top" alt="Article image">
                    <div class="card-body">
                        <h5 class="card-title">10 Healthy Recipes for a Better You</h5>
                        <p class="card-text text-secondary small">May 14, 2025 &bull; 6 min read</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 border-0 shadow-sm position-relative">
                    <a href="{{ url('/blog/startup-ideas-2025') }}" class="stretched-link"></a>
                    <span class="badge bg-info text-dark position-absolute m-2">Business</span>
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNlHdWYiA-2nXpEfxzzu-K8UGzPhMdtek1acZEYbyw0Q&s=10" class="card-img-top" alt="Article image">
                    <div class="card-body">
                        <h5 class="card-title">Startup Ideas to Watch in 2025</h5>
                        <p class="card-text text-secondary small">May 13, 2025 &bull; 7 min read</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ===== BROWSE BY CATEGORIES ===== -->
    <section class="container pb-5 my-3">
        <h3 class="fw-bold mb-4">Browse by Categories</h3>

        <div class="row g-3 text-center">

            <div class="col-6 col-md-2">
                <div class="border rounded-3 h-100 category-box position-relative">
                    <a href="{{ url('/category/technology') }}" class="stretched-link"></a>
                    <i class="bi bi-cpu fs-2 text-primary"></i>
                    <p class="fw-semibold mt-2 mb-0">Technology</p>
                    <small class="text-secondary">120 Articles</small>
                </div>
            </div>

            <div class="col-6 col-md-2">
                <div class="border rounded-3 h-100 category-box position-relative">
                    <a href="{{ url('/category/education') }}" class="stretched-link"></a>
                    <i class="bi bi-mortarboard fs-2 text-primary"></i>
                    <p class="fw-semibold mt-2 mb-0">Education</p>
                    <small class="text-secondary">95 Articles</small>
                </div>
            </div>

            <div class="col-6 col-md-2">
                <div class="border rounded-3 h-100 category-box position-relative">
                    <a href="{{ url('/category/travel') }}" class="stretched-link"></a>
                    <i class="bi bi-airplane fs-2 text-primary"></i>
                    <p class="fw-semibold mt-2 mb-0">Travel</p>
                    <small class="text-secondary">80 Articles</small>
                </div>
            </div>

            <div class="col-6 col-md-2">
                <div class="border rounded-3 h-100 category-box position-relative">
                    <a href="{{ url('/category/food') }}" class="stretched-link"></a>
                    <i class="bi bi-cup-hot fs-2 text-primary"></i>
                    <p class="fw-semibold mt-2 mb-0">Food</p>
                    <small class="text-secondary">60 Articles</small>
                </div>
            </div>

            <div class="col-6 col-md-2">
                <div class="border rounded-3 h-100 category-box position-relative">
                    <a href="{{ url('/category/business') }}" class="stretched-link"></a>
                    <i class="bi bi-briefcase fs-2 text-primary"></i>
                    <p class="fw-semibold mt-2 mb-0">Business</p>
                    <small class="text-secondary">70 Articles</small>
                </div>
            </div>

            <div class="col-6 col-md-2">
                <div class="border rounded-3 h-100 category-box position-relative">
                    <a href="{{ url('/category/health') }}" class="stretched-link"></a>
                    <i class="bi bi-heart-pulse fs-2 text-primary"></i>
                    <p class="fw-semibold mt-2 mb-0">Health</p>
                    <small class="text-secondary">40 Articles</small>
                </div>
            </div>

        </div>
    </section>

    @include('frontend.components.footer')

@endsection